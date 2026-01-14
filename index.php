<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'Final_DR';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            // Add new invoice
            $stmt = $conn->prepare("INSERT INTO main (si_number, dr_number, delivered_to, tin, address, terms, particulars, si_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "ssssssss",
                $_POST['si_number'],
                $_POST['dr_number'],
                $_POST['delivered_to'],
                $_POST['tin'],
                $_POST['address'],
                $_POST['terms'],
                $_POST['particulars'],
                $_POST['si_date']
            );

            if ($stmt->execute()) {
                $main_id = $stmt->insert_id;

                // Check if bnew_machine data was submitted
                if (isset($_POST['bnew_unit_type']) && is_array($_POST['bnew_unit_type'])) {
                    for ($i = 0; $i < count($_POST['bnew_unit_type']); $i++) {
                        $stmt_bnew = $conn->prepare("INSERT INTO bnew_machine (main_id, unit_type, machine_model, serial_no) VALUES (?, ?, ?, ?)");
                        $stmt_bnew->bind_param(
                            "isss",
                            $main_id,
                            $_POST['bnew_unit_type'][$i],
                            $_POST['bnew_machine_model'][$i],
                            $_POST['bnew_serial_no'][$i]
                        );
                        $stmt_bnew->execute();
                        $stmt_bnew->close();
                    }
                }

                $success_message = "Invoice added successfully!";
            } else {
                $error_message = "Error adding invoice: " . $stmt->error;
            }
            $stmt->close();
        } elseif ($_POST['action'] == 'update') {
            // Update invoice
            $stmt = $conn->prepare("UPDATE main SET si_number=?, dr_number=?, delivered_to=?, tin=?, address=?, terms=?, particulars=?, si_date=? WHERE id=?");
            $stmt->bind_param(
                "ssssssssi",
                $_POST['si_number'],
                $_POST['dr_number'],
                $_POST['delivered_to'],
                $_POST['tin'],
                $_POST['address'],
                $_POST['terms'],
                $_POST['particulars'],
                $_POST['si_date'],
                $_POST['id']
            );

            if ($stmt->execute()) {
                // Update related tables if needed
                $success_message = "Invoice updated successfully!";
            } else {
                $error_message = "Error updating invoice: " . $stmt->error;
            }
            $stmt->close();
        } elseif ($_POST['action'] == 'delete') {
            // Delete invoice and related records
            $main_id = intval($_POST['id']);

            // Start transaction
            $conn->begin_transaction();

            try {
                // Delete from all related tables first
                $tables = [
                    'bnew_machine',
                    'dr_invoice',
                    'dr_with_price',
                    'pullout_machine',
                    'replacement_machine',
                    'used_dr',
                    'used_machine'
                ];

                foreach ($tables as $table) {
                    $conn->query("DELETE FROM $table WHERE main_id = $main_id");
                }

                // Delete from main table
                $stmt = $conn->prepare("DELETE FROM main WHERE id = ?");
                $stmt->bind_param("i", $main_id);
                $stmt->execute();
                $stmt->close();

                $conn->commit();
                $success_message = "Invoice deleted successfully!";
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "Error deleting invoice: " . $e->getMessage();
            }
        }
    }

    // Refresh to show updated data
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get all invoices from main table ordered by most recent
$sql = "SELECT * FROM main ORDER BY created_at DESC, id DESC";
$result = $conn->query($sql);

// Check if we're viewing or editing a specific invoice
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$view_id = isset($_GET['view']) ? intval($_GET['view']) : 0;

// Get invoice for edit/view if specified
$edit_invoice = null;
$view_invoice = null;
$dr_with_prices = [];
$bnew_machines = [];
$used_machines = [];
$dr_invoices = [];
$replacement_machines = [];
$pullout_machines = [];
$used_drs = [];

// For Edit Section
if ($edit_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM main WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    $edit_invoice = $edit_result->fetch_assoc();
    $stmt->close();

    if ($edit_invoice['type'] == 'bnew') {
        // Get bnew_machine records for this invoice
        $stmt_bnew = $conn->prepare("SELECT * FROM bnew_machine WHERE dr_number = ?");
        $stmt_bnew->bind_param("i", $edit_invoice['dr_number']);
        $stmt_bnew->execute();
        $bnew_result = $stmt_bnew->get_result();
        while ($row = $bnew_result->fetch_assoc()) {
            $bnew_machines[] = $row;
        }
        $stmt_bnew->close();
    }

    if ($edit_invoice['type'] == 'usedmachine') {
        //Get used_machine records for this invoice
        $stmt_usedmachine = $conn->prepare("SELECT * FROM used_machine WHERE dr_number = ?");
        $stmt_usedmachine->bind_param("i", $edit_invoice['dr_number']);
        $stmt_usedmachine->execute();
        $usedmachine_result = $stmt_usedmachine->get_result();
        while ($row = $usedmachine_result->fetch_assoc()) {
            $used_machines[] = $row;
        }
        $stmt_usedmachine->close();
    }

    if ($edit_invoice['type'] == 'drinvoice') {
        //GEt DR Invoice records for this invoice
        $stmt_drinvoice = $conn->prepare('SELECT * FROM dr_invoice WHERE dr_number = ?');
        $stmt_drinvoice->bind_param('i', $edit_invoice['dr_number']);
        $stmt_drinvoice->execute();
        $drinvoice_result = $stmt_drinvoice->get_result();
        while ($rows = $drinvoice_result->fetch_assoc()) {
            $dr_invoices[] = $rows;
        }
        $stmt_drinvoice->close();
    }

    if ($edit_invoice['type'] == 'replacementmachine') {
        // Get replacement_machine records for this invoice
        $stmt_replacement = $conn->prepare('SELECT * FROM replacement_machine WHERE dr_number = ?');
        $stmt_replacement->bind_param('i', $edit_invoice['dr_number']);
        $stmt_replacement->execute();
        $replacement_result = $stmt_replacement->get_result();
        while ($rows = $replacement_result->fetch_assoc()) {
            $replacement_machines[] = $rows;
        }
        $stmt_replacement->close();
    }

    if ($edit_invoice['type'] == 'drwithprice') {
        // Get dr_with_price records for this invoice
        $stmt_drwithprice = $conn->prepare('SELECT * FROM dr_with_price WHERE dr_number = ?');
        $stmt_drwithprice->bind_param('i', $edit_invoice['dr_number']);
        $stmt_drwithprice->execute();
        $drwithprice_result = $stmt_drwithprice->get_result();
        while ($rows = $drwithprice_result->fetch_assoc()) {
            $dr_with_prices[] = $rows;
        }
        $stmt_drwithprice->close();
    }

    if ($edit_invoice['type'] == 'useddr') {
        // Get used_dr records for this invoice
        $stmt_useddr = $conn->prepare('SELECT * FROM used_dr WHERE dr_number = ?');
        $stmt_useddr->bind_param('i', $edit_invoice['dr_number']);
        $stmt_useddr->execute();
        $useddr_result = $stmt_useddr->get_result();
        while ($rows = $useddr_result->fetch_assoc()) {
            $used_drs[] = $rows;
        }
        $stmt_useddr->close();
    }

    if ($edit_invoice['type'] == 'pulloutmachine') {
        // Get pullout_machine records for this invoice
        $stmt_pullout = $conn->prepare('SELECT * FROM pullout_machine WHERE dr_number = ?');
        $stmt_pullout->bind_param('i', $edit_invoice['dr_number']);
        $stmt_pullout->execute();
        $pullout_result = $stmt_pullout->get_result();
        while ($rows = $pullout_result->fetch_assoc()) {
            $pullout_machines[] = $rows;
        }
        $stmt_pullout->close();
    }

    if ($edit_invoice['type'] == 'pulloutandreplacement') {
        // Get Pullout Machine records for this invoice
        $stmt_pullout = $conn->prepare('SELECT * FROM pullout_machine WHERE dr_number = ?');
        $stmt_pullout->bind_param('i', $view_invoice['dr_number']);
        $stmt_pullout->execute();
        $pullout_result = $stmt_pullout->get_result();
        while ($rows = $pullout_result->fetch_assoc()) {
            $pullout_machines[] = $rows;
        }
        $stmt_pullout->close();

        // GEet Replacement Machine records for this invoice
        $stmt_replacement = $conn->prepare('SELECT * FROM replacement_machine WHERE dr_number = ?');
        $stmt_replacement->bind_param('i', $view_invoice['dr_number']);
        $stmt_replacement->execute();
        $replacement_result = $stmt_replacement->get_result();
        while ($rows = $replacement_result->fetch_assoc()) {
            $replacement_machines[] = $rows;
        }
        $stmt_replacement->close();
    }
}

// For View Section
if ($view_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM main WHERE id = ?");
    $stmt->bind_param("i", $view_id);
    $stmt->execute();
    $view_result = $stmt->get_result();
    $view_invoice = $view_result->fetch_assoc();
    $stmt->close();

    if ($view_invoice['type'] == 'bnew') {
        // Get bnew_machine records for this invoice
        $stmt_bnew = $conn->prepare("SELECT * FROM bnew_machine WHERE dr_number = ?");
        $stmt_bnew->bind_param("i", $view_invoice['dr_number']);
        $stmt_bnew->execute();
        $bnew_result = $stmt_bnew->get_result();
        while ($row = $bnew_result->fetch_assoc()) {
            $bnew_machines[] = $row;
        }
        $stmt_bnew->close();
    }

    if ($view_invoice['type'] == 'usedmachine') {
        //Get used_machine records for this invoice
        $stmt_usedmachine = $conn->prepare("SELECT * FROM used_machine WHERE dr_number = ?");
        $stmt_usedmachine->bind_param("i", $view_invoice['dr_number']);
        $stmt_usedmachine->execute();
        $usedmachine_result = $stmt_usedmachine->get_result();
        while ($row = $usedmachine_result->fetch_assoc()) {
            $used_machines[] = $row;
        }
        $stmt_usedmachine->close();
    }

    if ($view_invoice['type'] == 'drinvoice') {
        //Get DR Invoice records for this invoice
        $stmt_drinvoice = $conn->prepare('SELECT * FROM dr_invoice WHERE dr_number = ?');
        $stmt_drinvoice->bind_param('i', $view_invoice['dr_number']);
        $stmt_drinvoice->execute();
        $drinvoice_result = $stmt_drinvoice->get_result();
        while ($rows = $drinvoice_result->fetch_assoc()) {
            $dr_invoices[] = $rows;
        }
        $stmt_drinvoice->close();
    }

    if ($view_invoice['type'] == 'replacementmachine') {
        // GEet Replacement Machine records for this invoice
        $stmt_replacement = $conn->prepare('SELECT * FROM replacement_machine WHERE dr_number = ?');
        $stmt_replacement->bind_param('i', $view_invoice['dr_number']);
        $stmt_replacement->execute();
        $replacement_result = $stmt_replacement->get_result();
        while ($rows = $replacement_result->fetch_assoc()) {
            $replacement_machines[] = $rows;
        }
        $stmt_replacement->close();
    }

    if ($view_invoice['type'] == 'drwithprice') {
        // Get Dr with price records for this invoice
        $stmt_drwithprice = $conn->prepare('SELECT * FROM dr_with_price WHERE dr_number = ?');
        $stmt_drwithprice->bind_param('i', $view_invoice['dr_number']);
        $stmt_drwithprice->execute();
        $drwithprice_result = $stmt_drwithprice->get_result();
        while ($rows = $drwithprice_result->fetch_assoc()) {
            $dr_with_prices[] = $rows;
        }
        $stmt_drwithprice->close();
    }

    if ($view_invoice['type'] == 'useddr') {
        // Get Used DR records for this invoice
        $stmt_useddr = $conn->prepare('SELECT * FROM used_dr WHERE dr_number = ?');
        $stmt_useddr->bind_param('i', $view_invoice['dr_number']);
        $stmt_useddr->execute();
        $useddr_result = $stmt_useddr->get_result();
        while ($rows = $useddr_result->fetch_assoc()) {
            $used_drs[] = $rows;
        }
        $stmt_useddr->close();
    }

    if ($view_invoice['type'] == 'pulloutmachine') {
        // Get Pullout Machine records for this invoice
        $stmt_pullout = $conn->prepare('SELECT * FROM pullout_machine WHERE dr_number = ?');
        $stmt_pullout->bind_param('i', $view_invoice['dr_number']);
        $stmt_pullout->execute();
        $pullout_result = $stmt_pullout->get_result();
        while ($rows = $pullout_result->fetch_assoc()) {
            $pullout_machines[] = $rows;
        }
        $stmt_pullout->close();
    }

    if ($view_invoice['type'] == 'pulloutandreplacement') {
        // Get Pullout Machine records for this invoice
        $stmt_pullout = $conn->prepare('SELECT * FROM pullout_machine WHERE dr_number = ?');
        $stmt_pullout->bind_param('i', $view_invoice['dr_number']);
        $stmt_pullout->execute();
        $pullout_result = $stmt_pullout->get_result();
        while ($rows = $pullout_result->fetch_assoc()) {
            $pullout_machines[] = $rows;
        }
        $stmt_pullout->close();

        // GEet Replacement Machine records for this invoice
        $stmt_replacement = $conn->prepare('SELECT * FROM replacement_machine WHERE dr_number = ?');
        $stmt_replacement->bind_param('i', $view_invoice['dr_number']);
        $stmt_replacement->execute();
        $replacement_result = $stmt_replacement->get_result();
        while ($rows = $replacement_result->fetch_assoc()) {
            $replacement_machines[] = $rows;
        }
        $stmt_replacement->close();
    }
}

// Get statistics
$total_invoices = $result->num_rows;

// Get today's count
$today = date('Y-m-d');
$sql_today = "SELECT COUNT(*) as count FROM main WHERE DATE(created_at) = '$today'";
$result_today = $conn->query($sql_today);
$today_count = $result_today->fetch_assoc()['count'];

// Get total bnew machines count
$sql_bnew = "SELECT COUNT(*) as count FROM bnew_machine";
$result_bnew = $conn->query($sql_bnew);
$bnew_count = $result_bnew->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Invoices Dashboard</title>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Show success/error messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <header class="header">
            <div class="header-title">
                <h1><i class="fas fa-file-invoice"></i> Recent Invoices Dashboard</h1>
                <p>Manage and track recently inserted invoice records</p>
            </div>
        </header>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Recent Invoices Table -->
            <div class="recent-invoices">
                <div class="section-header">
                    <h2>Recently Inserted Invoices</h2>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search invoices..." onkeyup="searchInvoices()">
                    </div>
                </div>

                <div class="table-container">
                    <table class="invoices-table">
                        <thead>
                            <tr>
                                <th style="min-width: 151px !important;">SI Number</th>
                                <th style="min-width: 151px !important;">DR Number</th>
                                <th>Delivered To</th>
                                <th style="min-width: 151px !important;">TIN</th>
                                <th>Address</th>
                                <th style="min-width: 151px !important;">Terms</th>
                                <th>Particulars</th>
                                <th style="min-width: 151px !important;">SI Date</th>
                                <!-- <th>Actions</th> -->
                                <th style="position: sticky; right: 0; background: #f8fafc;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="invoicesTableBody">
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr data-id="<?php echo $row['id']; ?>">
                                        <td><?php echo htmlspecialchars($row['si_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['dr_number']); ?></td>
                                        <td class="text-truncate"><?php echo htmlspecialchars($row['delivered_to']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tin']); ?></td>
                                        <td class="text-truncate"><?php echo htmlspecialchars($row['address']); ?></td>
                                        <td><?php echo htmlspecialchars($row['terms']); ?></td>
                                        <td class="text-truncate"><?php echo htmlspecialchars($row['particulars']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['si_date'])); ?></td>
                                        <td style="position: relative; z-index: 9999;">
                                            <div class="action-buttons">
                                                <button class="btn-action btn-view" onclick="viewInvoice(<?php echo htmlspecialchars($row['id']); ?>)" data-id="<?php echo $row['id']; ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn-action btn-edit" onclick="editInvoice(<?php echo htmlspecialchars($row['id']); ?>)" data-id="<?php echo $row['id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info">
                        Showing all <span id="totalRows"><?php echo $total_invoices; ?></span> invoices
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Invoice Modal -->
    <?php if ($edit_invoice): ?>
        <div class="modal" id="editInvoiceModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Edit Invoice</h3>
                    <button class="modal-close" onclick="closeModal('editInvoiceModal')">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="si_number">SI Number *</label>
                                <input type="text" name="si_number" class="form-control" value="<?php echo htmlspecialchars($edit_invoice['si_number']); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="dr_number">DR Number *</label>
                                <input type="text" name="dr_number" class="form-control" value="<?php echo htmlspecialchars($edit_invoice['dr_number']); ?>" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="delivered_to">Delivered To *</label>
                            <input type="text" name="delivered_to" class="form-control" value="<?php echo htmlspecialchars($edit_invoice['delivered_to']); ?>" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="tin">TIN *</label>
                                <input type="text" name="tin" class="form-control" value="<?php echo htmlspecialchars($edit_invoice['tin']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="si_date">SI Date *</label>
                                <input type="date" name="si_date" class="form-control" value="<?php echo $edit_invoice['si_date']; ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Address *</label>
                            <textarea name="address" class="form-control" rows="2" required><?php echo htmlspecialchars($edit_invoice['address']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="terms">Terms *</label>
                            <input type="text" name="terms" class="form-control" value="<?php echo htmlspecialchars($edit_invoice['terms']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="particulars">Particulars</label>
                            <textarea name="particulars" class="form-control" rows="3"><?php echo htmlspecialchars($edit_invoice['particulars']); ?></textarea>
                        </div>

                        <!-- for DR Invoice -->
                        <?php if (!empty($dr_invoices)):
                            $dr_invoice = $dr_invoices[0];
                        ?>
                            <div class="form-group">
                                <label for="machine_model">Machine Model</label>
                                <input type="text" name="machine_model" class="form-control" value="<?php echo htmlspecialchars($dr_invoice['machine_model']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="under_po_no">Under P.O No.</label>
                                <input type="text" name="under_po_no" class="form-control" value="<?php echo htmlspecialchars($dr_invoice['under_po_no']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="under_invoice_no">Under Invoice No.</label>
                                <input type="text" name="under_invoice_no" class="form-control" value="<?php echo htmlspecialchars($dr_invoice['under_invoice_no']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="note">Notes</label>
                                <textarea name="note" class="form-control" rows="3"><?php echo htmlspecialchars($dr_invoice['note']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="delivery_type">Delivery Status</label>
                                <select name="delivery_type" class="form-control">
                                    <option value="partial" <?php echo ($dr_invoice['delivery_type'] == 'partial') ? 'selected' : ''; ?>>Partial</option>
                                    <option value="complete" <?php echo ($dr_invoice['delivery_type'] == 'complete') ? 'selected' : ''; ?>>Complete</option>
                                </select>
                            </div>
                        <?php endif; ?>


                        <!-- For Used DR -->
                        <?php if (!empty($used_drs)):
                            $used_dr = $used_drs[0];
                        ?>
                            <div class="form-group">
                                <label for="technician_name">Technician Name</label>
                                <input type="text" name="technician_name" class="form-control" value="<?php echo htmlspecialchars($used_dr['technician_name']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="machine_model">Machine Model</label>
                                <input type="text" name="machine_model" class="form-control" value="<?php echo htmlspecialchars($used_dr['machine_model']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="serial_number">Serial Number</label>
                                <input type="text" name="serial_number" class="form-control" value="<?php echo htmlspecialchars($used_dr['serial_no']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="pr_number">PR Number</label>
                                <input type="text" name="pr_number" class="form-control" value="<?php echo htmlspecialchars($used_dr['pr_number']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="mr_number">MR Number</label>
                                <input type="text" name="mr_number" class="form-control" value="<?php echo htmlspecialchars($used_dr['mr_start']); ?>">
                            </div>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- BNew Machines Section -->
                <?php if (!empty($bnew_machines)): ?>
                    <div class="bnew-section">
                        <h4><i class="fas fa-robot"></i> BNew Machines</h4>
                        <?php foreach ($bnew_machines as $index => $machine): ?>
                            <div class="bnew-row">
                                <div class="form-group">
                                    <label>Unit Type</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['unit_type']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Machine Model</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['machine_model']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Serial No.</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['serial_no']); ?>" readonly>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Used Machines Section -->
                <?php if (!empty($used_machines)): ?>
                    <div class="used-machine-section">
                        <h4><i class="fas fa-cogs"></i> Used Machines</h4>
                        <?php foreach ($used_machines as $index => $machine): ?>
                            <div class="used-machine-row">
                                <div class="form-group">
                                    <label>Unit Type</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['unit_type']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Machine Model</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['machine_model']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Serial Number</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['serial_no']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>MR Start</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['mr_start']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Color Impression</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['color_impression']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Black Impression</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['black_impression']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Color Large Impression</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['color_large_impression']); ?>" readonly>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Edit Replacement Machines Section -->
                <?php if (!empty($replacement_machines)): ?>
                    <div class="replacement-machine-section">
                        <h4><i class="fas fa-cogs"></i> Replacement Machines</h4>
                        <?php foreach ($replacement_machines as $index => $machine): ?>
                            <div class="replacement-machine-row">
                                <div class="form-group">
                                    <label>Unit Type</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['unit_type']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Machine Model</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['machine_model']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Serial Number</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['serial_no']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>MR Start</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['mr_start']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Color Impression</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['color_impression']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Black Impression</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['black_impression']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Color Large Impression</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['color_large_impression']); ?>" readonly>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Edit DR Invoice Section -->
                <?php if (!empty($dr_invoices)): ?>
                    <div class="dr-invoice-section">
                        <h4><i class="fas fa-file-invoice"></i> DR Invoices</h4>
                        <?php foreach ($dr_invoices as $index => $machine): ?>
                            <div class="dr-invoice-row">
                                <div class="form-group">
                                    <label>Unit Type</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['unit_type']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['quantity']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Item Description</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['item_description']); ?>" readonly>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Edit DR with Price Section -->
                <?php if (!empty($dr_with_prices)): ?>
                    <div class="dr-with-price-section">
                        <h4><i class="fas fa-file-invoice-dollar"></i> DR with Price</h4>
                        <?php foreach ($dr_with_prices as $index => $machine): ?>
                            <div class="dr-with-price-row">
                                <div class="form-group">
                                    <label>Machine Model</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['machine_model']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['quantity']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Price</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['price']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Total Price</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['total']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Unit Type</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['unit_type']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Item Description</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['item_description']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>PR Number</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['item_description']); ?>" readonly>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Edit Used DR Section -->
                <?php if (!empty($used_drs)): ?>
                    <div class="used-dr-section">
                        <h4><i class="fas fa-file-invoice"></i> Used DR</h4>
                        <?php foreach ($used_drs as $index => $machine): ?>
                            <div class="used-dr-row">
                                <div class="form-group">
                                    <label>Item Description</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['item_description']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Unit Type</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['unit_type']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['quantity']); ?>" readonly>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>


                <!-- Edit Pullout Machines Section -->
                <?php if (!empty($pullout_machines)): ?>
                    <div class="pullout-machine-section">
                        <h4><i class="fas fa-cogs"></i>Pullout Machines</h4>
                        <?php foreach ($pullout_machines as $index => $machine): ?>
                            <div class="pullout-machine-row">
                                <div class="form-group">
                                    <label>Machine Model</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['machine_model']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Serial Number</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['serial_no']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>MR End</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['mr_end']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Color Impression</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['color_impression']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Black Impression</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['black_impression']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Color Large Impression</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($machine['color_large_impression']); ?>" readonly>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="form-actions">
                    <input type="hidden" name="id" value="<?php echo $edit_invoice['id']; ?>">
                    <input type="hidden" name="action" value="update">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editInvoiceModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Invoice</button>
                </div>
                </form>
            </div>
        </div>
        </div>
    <?php endif; ?>

    <!-- View Invoice Modal -->
    <?php if ($view_invoice): ?>
        <div class="modal" id="viewInvoiceModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>View Invoice</h3>
                    <button class="modal-close" onclick="closeModal('viewInvoiceModal')">&times;</button>
                </div>
                <div class="modal-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="invoice-detail-item">
                            <div class="detail-label">SI Number</div>
                            <div class="detail-value"><?php echo htmlspecialchars($view_invoice['si_number']); ?></div>
                        </div>
                        <div class="invoice-detail-item">
                            <div class="detail-label">DR Number</div>
                            <div class="detail-value"><?php echo htmlspecialchars($view_invoice['dr_number']); ?></div>
                        </div>
                        <div class="invoice-detail-item" style="grid-column: span 2;">
                            <div class="detail-label">Delivered To</div>
                            <div class="detail-value"><?php echo htmlspecialchars($view_invoice['delivered_to']); ?></div>
                        </div>
                        <div class="invoice-detail-item">
                            <div class="detail-label">TIN</div>
                            <div class="detail-value"><?php echo htmlspecialchars($view_invoice['tin']); ?></div>
                        </div>
                        <div class="invoice-detail-item">
                            <div class="detail-label">SI Date</div>
                            <div class="detail-value"><?php echo date('F d, Y', strtotime($view_invoice['si_date'])); ?></div>
                        </div>
                        <div class="invoice-detail-item" style="grid-column: span 2;">
                            <div class="detail-label">Address</div>
                            <div class="detail-value"><?php echo nl2br(htmlspecialchars($view_invoice['address'])); ?></div>
                        </div>
                        <div class="invoice-detail-item">
                            <div class="detail-label">Terms</div>
                            <div class="detail-value"><?php echo htmlspecialchars($view_invoice['terms']); ?></div>
                        </div>
                        <div class="invoice-detail-item">
                            <div class="detail-label">Created At</div>
                            <div class="detail-value"><?php echo date('F d, Y H:i', strtotime($view_invoice['created_at'])); ?></div>
                        </div>
                        <div class="invoice-detail-item">
                            <div class="detail-label">Particulars</div>
                            <div class="detail-value"><?php echo nl2br(htmlspecialchars($view_invoice['particulars'])); ?></div>
                        </div>

                        <!-- for DR Invoice -->
                        <?php if (!empty($dr_invoices)):
                            // Access only the first record in the array
                            $dr_invoice = $dr_invoices[0];
                        ?>
                            <div class="invoice-detail-item">
                                <div class="detail-label">Machine Model</div>
                                <div class="detail-value"><?php echo nl2br(htmlspecialchars($dr_invoice['machine_model'])); ?></div>
                            </div>
                            <div class="invoice-detail-item">
                                <div class="detail-label">Under P.O No.</div>
                                <div class="detail-value"><?php echo nl2br(htmlspecialchars($dr_invoice['under_po_no'])); ?></div>
                            </div>
                            <div class="invoice-detail-item">
                                <div class="detail-label">Under Invoice No.</div>
                                <div class="detail-value"><?php echo nl2br(htmlspecialchars($dr_invoice['under_invoice_no'])); ?></div>
                            </div>
                            <div class="invoice-detail-item">
                                <div class="detail-label">Notes</div>
                                <div class="detail-value"><?php echo nl2br(htmlspecialchars($dr_invoice['note'])); ?></div>
                            </div>
                            <div class="invoice-detail-item">
                                <div class="detail-label">Delivery Status</div>
                                <div class="detail-value"><?php echo nl2br(htmlspecialchars($dr_invoice['delivery_type'])); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($used_drs)):
                            $used_dr = $used_drs[0];
                        ?>
                            <div class="invoice-detail-item">
                                <div class="detail-label">Technician Name</div>
                                <div class="detail-value"><?php echo nl2br(htmlspecialchars($used_dr['technician_name'])); ?></div>
                            </div>
                            <div class="invoice-detail-item">
                                <div class="detail-label">Machine Model</div>
                                <div class="detail-value"><?php echo nl2br(htmlspecialchars($used_dr['machine_model'])); ?></div>
                            </div>
                            <div class="invoice-detail-item">
                                <div class="detail-label">Serial Number</div>
                                <div class="detail-value"><?php echo nl2br(htmlspecialchars($used_dr['serial_no'])); ?></div>
                            </div>
                            <div class="invoice-detail-item">
                                <div class="detail-label">PR Number</div>
                                <div class="detail-value"><?php echo nl2br(htmlspecialchars($used_dr['pr_number'])); ?></div>
                            </div>
                            <div class="invoice-detail-item">
                                <div class="detail-label">MR Start</div>
                                <div class="detail-value"><?php echo nl2br(htmlspecialchars($used_dr['mr_start'])); ?></div>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- View Bnew Machine -->
                    <?php if (!empty($bnew_machines)): ?>
                        <div class="bnew-section" style="margin-top: 20px;">
                            <h4><i class="fas fa-robot"></i> BNew Machines</h4>
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #e9ecef;">
                                        <th style="padding: 10px; text-align: left;">Unit Type</th>
                                        <th style="padding: 10px; text-align: left;">Machine Model</th>
                                        <th style="padding: 10px; text-align: left;">Serial No.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bnew_machines as $machine): ?>
                                        <tr style="border-bottom: 1px solid #dee2e6;">
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['unit_type']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['machine_model']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['serial_no']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <!-- View Used Machine -->
                    <?php if (!empty($used_machines)): ?>
                        <div class="used-machine-section" style="margin-top: 20px;">
                            <h4><i class="fas fa-cogs"></i> Used Machines</h4>
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #e9ecef;">
                                        <th style="padding: 10px; text-align: left;">Unit Type</th>
                                        <th style="padding: 10px; text-align: left;">Machine Model</th>
                                        <th style="padding: 10px; text-align: left;">Serial No.</th>
                                        <th style="padding: 10px; text-align: left;">MR Start</th>
                                        <th style="padding: 10px; text-align: left;">Color Impression</th>
                                        <th style="padding: 10px; text-align: left;">Black Impression</th>
                                        <th style="padding: 10px; text-align: left;">Color Large Impression</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($used_machines as $machine): ?>
                                        <tr style="border-bottom: 1px solid #dee2e6;">
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['unit_type']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['machine_model']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['serial_no']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['mr_start']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['color_impression']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['black_impression']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['color_large_impression']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <!-- View Replacement Machine -->
                    <?php if (!empty($replacement_machines)): ?>

                        <div class="replacement-machine-section" style="margin-top: 20px;">
                            <h4><i class="fas fa-cogs"></i> Replacement Machines</h4>
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #e9ecef;">
                                        <th style="padding: 10px; text-align: left;">Unit Type</th>
                                        <th style="padding: 10px; text-align: left;">Machine Model</th>
                                        <th style="padding: 10px; text-align: left;">Serial No.</th>
                                        <th style="padding: 10px; text-align: left;">MR Start</th>
                                        <th style="padding: 10px; text-align: left;">Color Impression</th>
                                        <th style="padding: 10px; text-align: left;">Black Impression</th>
                                        <th style="padding: 10px; text-align: left;">Color Large Impression</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($replacement_machines as $machine): ?>
                                        <tr style="border-bottom: 1px solid #dee2e6;">
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['unit_type']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['machine_model']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['serial_no']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['mr_start']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['color_impression']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['black_impression']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['color_large_impression']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <!-- View DR Invoice -->
                    <?php if (!empty($dr_invoices)): ?>
                        <div class="dr-invoice-section" style="margin-top: 20px;">
                            <h4><i class="fas fa-file-invoice"></i> DR Invoice</h4>
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #e9ecef;">
                                        <th style="padding: 10px; text-align: left;">Unit Type</th>
                                        <th style="padding: 10px; text-align: left;">Quantity</th>
                                        <th style="padding: 10px; text-align: left;">Item Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dr_invoices as $machine): ?>
                                        <tr style="border-bottom: 1px solid #dee2e6;">
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['unit_type']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['quantity']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['item_description']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <!-- View DR with Price -->
                    <?php if (!empty($dr_with_prices)): ?>
                        <div class="dr-with-price-section" style="margin-top: 20px;">
                            <h4><i class="fas fa-file-invoice-dollar"></i> DR with Price</h4>
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #e9ecef;">
                                        <th style="padding: 10px; text-align: left;">Machine Model</th>
                                        <th style="padding: 10px; text-align: left;">Quantity</th>
                                        <th style="padding: 10px; text-align: left;">Price</th>
                                        <th style="padding: 10px; text-align: left;">Total</th>
                                        <th style="padding: 10px; text-align: left;">Unit Type</th>
                                        <th style="padding: 10px; text-align: left;">Item Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dr_with_prices as $machine): ?>
                                        <tr style="border-bottom: 1px solid #dee2e6;">
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['machine_model']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['quantity']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['price']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['total']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['unit_type']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['item_description']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <!-- View Used DR -->
                    <?php if (!empty($used_drs)): ?>
                        <div class="used-dr-section" style="margin-top: 20px;">
                            <h4><i class="fas fa-file-invoice-dollar"></i> Used DR</h4>
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #e9ecef;">
                                        <th style="padding: 10px; text-align: left;">Item Description</th>
                                        <th style="padding: 10px; text-align: left;">Unit Type</th>
                                        <th style="padding: 10px; text-align: left;">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($used_drs as $machine): ?>
                                        <tr style="border-bottom: 1px solid #dee2e6;">
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['item_description']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['unit_type']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['quantity']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <!-- View Pullout Machines -->
                    <?php if (!empty($pullout_machines)): ?>
                        <div class="pullout-machine-section" style="margin-top: 20px;">
                            <h4><i class="fas fa-file-invoice-dollar"></i> Pullout Machines</h4>
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #e9ecef;">
                                        <th style="padding: 10px; text-align: left;">Machine Model</th>
                                        <th style="padding: 10px; text-align: left;">Serial No</th>
                                        <th style="padding: 10px; text-align: left;">MR End</th>
                                        <th style="padding: 10px; text-align: left;">Color Impression</th>
                                        <th style="padding: 10px; text-align: left;">Black Impression</th>
                                        <th style="padding: 10px; text-align: left;">Color Large Impression</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pullout_machines as $index => $machine): ?>
                                        <tr style="border-bottom: 1px solid #dee2e6;">
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['machine_model']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['serial_no']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['mr_end']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['color_impression']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['black_impression']); ?></td>
                                            <td style="padding: 10px;"><?php echo htmlspecialchars($machine['color_large_impression']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <!-- View Pullout and Replace Machines -->

                    <div class="form-actions">
                        <a href="?edit=<?php echo $view_invoice['id']; ?>" class="btn btn-primary">Edit Invoice</a>
                        <button type="button" class="btn btn-secondary" onclick="closeModal('viewInvoiceModal')">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="Mainscript.js"></script>
</body>

</html>

<?php
// Close connection
$conn->close();
?>