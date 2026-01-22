<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'Final_DR');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Enable error reporting for MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Helper function to clean numeric input
function cleanNumeric($value)
{
    return floatval(str_replace(',', '', $value ?? '0'));
}

// Helper function to fetch related records
function getRelatedRecords($conn, $table, $dr_number)
{
    $stmt = $conn->prepare("SELECT * FROM $table WHERE dr_number = ?");
    $stmt->bind_param("i", $dr_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    $stmt->close();
    return $records;
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
            // Start transaction for update
            $conn->begin_transaction();

            try {
                $main_id = intval($_POST['id']);
                $dr_number = $_POST['dr_number'];

                // Get current invoice type to know which tables to update
                $stmt_get_type = $conn->prepare("SELECT type FROM main WHERE id = ?");
                $stmt_get_type->bind_param("i", $main_id);
                $stmt_get_type->execute();
                $stmt_get_type->bind_result($invoice_type);
                $stmt_get_type->fetch();
                $stmt_get_type->close();

                // Update main table
                $stmt = $conn->prepare("UPDATE main SET 
                    si_number = ?, 
                    dr_number = ?, 
                    delivered_to = ?, 
                    tin = ?, 
                    address = ?, 
                    terms = ?, 
                    particulars = ?, 
                    si_date = ? 
                    WHERE id = ?");

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
                    $main_id
                );

                $stmt->execute();
                $stmt->close();

                // Handle different invoice types
                switch ($invoice_type) {
                    case 'bnew':
                        // Delete existing bnew_machine records
                        $stmt_delete = $conn->prepare("DELETE FROM bnew_machine WHERE dr_number = ?");
                        $stmt_delete->bind_param("i", $dr_number);
                        $stmt_delete->execute();
                        $stmt_delete->close();

                        // Insert updated bnew_machine records if they exist
                        if (isset($_POST['bnew_unit_type']) && is_array($_POST['bnew_unit_type'])) {
                            for ($i = 0; $i < count($_POST['bnew_unit_type']); $i++) {
                                if (!empty($_POST['bnew_unit_type'][$i])) {
                                    $stmt_bnew = $conn->prepare("INSERT INTO bnew_machine (dr_number, unit_type, machine_model, serial_no) VALUES (?, ?, ?, ?)");
                                    $stmt_bnew->bind_param(
                                        "isss",
                                        $dr_number,
                                        $_POST['bnew_unit_type'][$i],
                                        $_POST['bnew_machine_model'][$i],
                                        $_POST['bnew_serial_no'][$i]
                                    );
                                    $stmt_bnew->execute();
                                    $stmt_bnew->close();
                                }
                            }
                        }
                        break;

                    case 'usedmachine':
                        // Delete existing used_machine records
                        $stmt_delete = $conn->prepare("DELETE FROM used_machine WHERE dr_number = ?");
                        $stmt_delete->bind_param("i", $dr_number);
                        $stmt_delete->execute();
                        $stmt_delete->close();

                        // Insert updated used_machine records
                        if (isset($_POST['used_unit_type']) && is_array($_POST['used_unit_type'])) {
                            for ($i = 0; $i < count($_POST['used_unit_type']); $i++) {
                                if (!empty($_POST['used_unit_type'][$i])) {
                                    // Clean numeric fields using helper function
                                    $mr_start = cleanNumeric($_POST['used_mr_start'][$i] ?? '0');
                                    $color_imp = cleanNumeric($_POST['used_color_imp'][$i] ?? '0');
                                    $black_imp = cleanNumeric($_POST['used_black_imp'][$i] ?? '0');
                                    $color_large_imp = cleanNumeric($_POST['used_color_large_imp'][$i] ?? '0');

                                    $stmt_used = $conn->prepare("INSERT INTO used_machine (dr_number, unit_type, machine_model, serial_no, mr_start, color_impression, black_impression, color_large_impression) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                                    $stmt_used->bind_param(
                                        "isssdddd",
                                        $dr_number,
                                        $_POST['used_unit_type'][$i],
                                        $_POST['used_machine_model'][$i],
                                        $_POST['used_serial_no'][$i],
                                        $mr_start,
                                        $color_imp,
                                        $black_imp,
                                        $color_large_imp
                                    );
                                    $stmt_used->execute();
                                    $stmt_used->close();
                                }
                            }
                        }
                        break;

                    case 'drinvoice':
                        // Delete existing dr_invoice records
                        $stmt_delete = $conn->prepare("DELETE FROM dr_invoice WHERE dr_number = ?");
                        $stmt_delete->bind_param("i", $dr_number);
                        $stmt_delete->execute();
                        $stmt_delete->close();

                        // Insert updated dr_invoice records
                        if (isset($_POST['invoice_quantity']) && is_array($_POST['invoice_quantity'])) {
                            for ($i = 0; $i < count($_POST['invoice_quantity']); $i++) {
                                if (!empty($_POST['invoice_quantity'][$i])) {
                                    $quantity = str_replace(',', '', $_POST['invoice_quantity'][$i]);

                                    $stmt_invoice = $conn->prepare("INSERT INTO dr_invoice (dr_number, machine_model, under_po_no, under_invoice_no, note, delivery_type, quantity, unit_type, item_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                    $stmt_invoice->bind_param(
                                        "isssssiss",
                                        $dr_number,
                                        $_POST['invoice_machine_model'][$i],
                                        $_POST['invoice_under_po_no'][$i],
                                        $_POST['invoice_under_invoice_no'][$i],
                                        $_POST['invoice_note'][$i],
                                        $_POST['delivery_type'],
                                        $quantity,
                                        $_POST['invoice_unit_type'][$i],
                                        $_POST['invoice_item_desc'][$i]
                                    );
                                    $stmt_invoice->execute();
                                    $stmt_invoice->close();
                                }
                            }
                        }
                        break;

                    case 'replacementmachine':
                        // Delete existing replacement_machine records
                        $stmt_delete = $conn->prepare("DELETE FROM replacement_machine WHERE dr_number = ?");
                        $stmt_delete->bind_param("i", $dr_number);
                        $stmt_delete->execute();
                        $stmt_delete->close();

                        // Insert updated replacement_machine records
                        if (isset($_POST['replace_unit_type']) && is_array($_POST['replace_unit_type'])) {
                            for ($i = 0; $i < count($_POST['replace_unit_type']); $i++) {
                                if (!empty($_POST['replace_unit_type'][$i])) {
                                    // Clean numeric fields using helper function
                                    $mr_start = cleanNumeric($_POST['replace_mr_start'][$i] ?? '0');
                                    $color_imp = cleanNumeric($_POST['replace_color_imp'][$i] ?? '0');
                                    $black_imp = cleanNumeric($_POST['replace_black_imp'][$i] ?? '0');
                                    $color_large_imp = cleanNumeric($_POST['replace_color_large_imp'][$i] ?? '0');

                                    $stmt_replace = $conn->prepare("INSERT INTO replacement_machine (dr_number, unit_type, machine_model, serial_no, mr_start, color_impression, black_impression, color_large_impression) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                                    $stmt_replace->bind_param(
                                        "isssdddd",
                                        $dr_number,
                                        $_POST['replace_unit_type'][$i],
                                        $_POST['replace_machine_model'][$i],
                                        $_POST['replace_serial_no'][$i],
                                        $mr_start,
                                        $color_imp,
                                        $black_imp,
                                        $color_large_imp
                                    );
                                    $stmt_replace->execute();
                                    $stmt_replace->close();
                                }
                            }
                        }
                        break;

                    case 'drwithprice':
                        // Delete existing dr_with_price records
                        $stmt_delete = $conn->prepare("DELETE FROM dr_with_price WHERE dr_number = ?");
                        $stmt_delete->bind_param("i", $dr_number);
                        $stmt_delete->execute();
                        $stmt_delete->close();

                        // Insert updated dr_with_price records
                        if (isset($_POST['price_quantity']) && is_array($_POST['price_quantity'])) {
                            for ($i = 0; $i < count($_POST['price_quantity']); $i++) {
                                if (!empty($_POST['price_quantity'][$i])) {
                                    $quantity = str_replace(',', '', $_POST['price_quantity'][$i]);
                                    $price = str_replace(',', '', $_POST['price'][$i]);
                                    $total = $quantity * $price;

                                    $stmt_price = $conn->prepare("INSERT INTO dr_with_price (dr_number, machine_model, quantity, price, total, unit_type, item_description) VALUES (?, ?, ?, ?, ?, ?, ?)");
                                    $stmt_price->bind_param(
                                        "isddiss",
                                        $dr_number,
                                        $_POST['price_machine_model'][$i],
                                        $quantity,
                                        $price,
                                        $total,
                                        $_POST['price_unit_type'][$i],
                                        $_POST['price_item_desc'][$i]
                                    );
                                    $stmt_price->execute();
                                    $stmt_price->close();
                                }
                            }
                        }
                        break;

                    case 'useddr':
                        // Delete existing used_dr records
                        $stmt_delete = $conn->prepare("DELETE FROM used_dr WHERE dr_number = ?");
                        $stmt_delete->bind_param("i", $dr_number);
                        $stmt_delete->execute();
                        $stmt_delete->close();

                        // Insert updated used_dr records
                        if (isset($_POST['useddr_quantity']) && is_array($_POST['useddr_quantity'])) {
                            for ($i = 0; $i < count($_POST['useddr_quantity']); $i++) {
                                if (!empty($_POST['useddr_quantity'][$i])) {
                                    $quantity = str_replace(',', '', $_POST['useddr_quantity'][$i]);
                                    $mr_start = str_replace(',', '', $_POST['useddr_mr_start'][$i] ?? '0');

                                    $stmt_useddr = $conn->prepare("INSERT INTO used_dr (dr_number, machine_model, serial_no, mr_start, technician_name, pr_number, quantity, unit_type, item_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                    $stmt_useddr->bind_param(
                                        "isssssiss",
                                        $dr_number,
                                        $_POST['useddr_machine_model'][$i],
                                        $_POST['useddr_serial_no'][$i],
                                        $mr_start,
                                        $_POST['useddr_technician_name'][$i],
                                        $_POST['useddr_pr_number'][$i],
                                        $quantity,
                                        $_POST['useddr_unit_type'][$i],
                                        $_POST['useddr_item_desc'][$i]
                                    );
                                    $stmt_useddr->execute();
                                    $stmt_useddr->close();
                                }
                            }
                        }
                        break;

                    case 'pulloutmachine':
                        // Delete existing pullout_machine records
                        $stmt_delete = $conn->prepare("DELETE FROM pullout_machine WHERE dr_number = ?");
                        $stmt_delete->bind_param("i", $dr_number);
                        $stmt_delete->execute();
                        $stmt_delete->close();

                        // Insert updated pullout_machine records
                        if (isset($_POST['pullout_machine_model']) && is_array($_POST['pullout_machine_model'])) {
                            for ($i = 0; $i < count($_POST['pullout_machine_model']); $i++) {
                                if (!empty($_POST['pullout_machine_model'][$i])) {
                                    // Clean numeric fields
                                    $mr_end = str_replace(',', '', $_POST['pullout_mr_end'][$i] ?? '0');
                                    $color_imp = str_replace(',', '', $_POST['pullout_color_imp'][$i] ?? '0');
                                    $black_imp = str_replace(',', '', $_POST['pullout_black_imp'][$i] ?? '0');
                                    $color_large_imp = str_replace(',', '', $_POST['pullout_color_large_imp'][$i] ?? '0');

                                    $stmt_pullout = $conn->prepare("INSERT INTO pullout_machine (dr_number, machine_model, serial_no, mr_end, color_impression, black_impression, color_large_impression) VALUES (?, ?, ?, ?, ?, ?, ?)");
                                    $stmt_pullout->bind_param(
                                        "isssddd",
                                        $dr_number,
                                        $_POST['pullout_machine_model'][$i],
                                        $_POST['pullout_serial_no'][$i],
                                        $mr_end,
                                        $color_imp,
                                        $black_imp,
                                        $color_large_imp
                                    );
                                    $stmt_pullout->execute();
                                    $stmt_pullout->close();
                                }
                            }
                        }
                        break;

                    case 'pulloutandreplacement':
                        // Delete existing pullout_machine records
                        $stmt_delete = $conn->prepare("DELETE FROM pullout_machine WHERE dr_number = ?");
                        $stmt_delete->bind_param("i", $dr_number);
                        $stmt_delete->execute();
                        $stmt_delete->close();

                        // Delete existing replacement_machine records
                        $stmt_delete2 = $conn->prepare("DELETE FROM replacement_machine WHERE dr_number = ?");
                        $stmt_delete2->bind_param("i", $dr_number);
                        $stmt_delete2->execute();
                        $stmt_delete2->close();

                        // Insert updated pullout_machine records
                        if (isset($_POST['pullout_machine_model']) && is_array($_POST['pullout_machine_model'])) {
                            for ($i = 0; $i < count($_POST['pullout_machine_model']); $i++) {
                                if (!empty($_POST['pullout_machine_model'][$i])) {
                                    // Clean numeric fields
                                    $mr_end = str_replace(',', '', $_POST['pullout_mr_end'][$i] ?? '0');
                                    $color_imp = str_replace(',', '', $_POST['pullout_color_imp'][$i] ?? '0');
                                    $black_imp = str_replace(',', '', $_POST['pullout_black_imp'][$i] ?? '0');
                                    $color_large_imp = str_replace(',', '', $_POST['pullout_color_large_imp'][$i] ?? '0');

                                    $stmt_pullout = $conn->prepare("INSERT INTO pullout_machine (dr_number, machine_model, serial_no, mr_end, color_impression, black_impression, color_large_impression) VALUES (?, ?, ?, ?, ?, ?, ?)");
                                    $stmt_pullout->bind_param(
                                        "isssddd",
                                        $dr_number,
                                        $_POST['pullout_machine_model'][$i],
                                        $_POST['pullout_serial_no'][$i],
                                        $mr_end,
                                        $color_imp,
                                        $black_imp,
                                        $color_large_imp
                                    );
                                    $stmt_pullout->execute();
                                    $stmt_pullout->close();
                                }
                            }
                        }

                        // Insert updated replacement_machine records
                        if (isset($_POST['replace_machine_model']) && is_array($_POST['replace_machine_model'])) {
                            for ($i = 0; $i < count($_POST['replace_machine_model']); $i++) {
                                if (!empty($_POST['replace_machine_model'][$i])) {
                                    // Clean numeric fields using helper function
                                    $mr_start = cleanNumeric($_POST['replace_mr_start'][$i] ?? '0');
                                    $color_imp = cleanNumeric($_POST['replace_color_imp'][$i] ?? '0');
                                    $black_imp = cleanNumeric($_POST['replace_black_imp'][$i] ?? '0');
                                    $color_large_imp = cleanNumeric($_POST['replace_color_large_imp'][$i] ?? '0');

                                    $stmt_replace = $conn->prepare("INSERT INTO replacement_machine (dr_number, machine_model, serial_no, mr_start, color_impression, black_impression, color_large_impression) VALUES (?, ?, ?, ?, ?, ?, ?)");
                                    $stmt_replace->bind_param(
                                        "isssddd",
                                        $dr_number,
                                        $_POST['replace_machine_model'][$i],
                                        $_POST['replace_serial_no'][$i],
                                        $mr_start,
                                        $color_imp,
                                        $black_imp,
                                        $color_large_imp
                                    );
                                    $stmt_replace->execute();
                                    $stmt_replace->close();
                                }
                            }
                        }
                        break;
                }

                $conn->commit();
                $success_message = "Invoice updated successfully!";
                header("Location: " . $_SERVER['PHP_SELF'] . "?edit=" . $main_id);
                exit();
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "Error updating invoice: " . $e->getMessage();
            }
        } elseif ($_POST['action'] == 'delete') {
            // Delete invoice and related records
            $main_id = intval($_POST['id']);

            // Start transaction
            $conn->begin_transaction();

            try {
                // Delete from all related tables first using prepared statements
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
                    $delete_stmt = $conn->prepare("DELETE FROM $table WHERE main_id = ?");
                    $delete_stmt->bind_param("i", $main_id);
                    $delete_stmt->execute();
                    $delete_stmt->close();
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
    // header("Location: " . $_SERVER['PHP_SELF']);

    // Only redirect if NOT already viewing/editing
    if (!isset($_GET['edit']) && !isset($_GET['view'])) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
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

    if ($edit_invoice) {
        $dr_number = $edit_invoice['dr_number'];
        $type = $edit_invoice['type'];

        // Load related records based on type
        switch ($type) {
            case 'bnew':
                $bnew_machines = getRelatedRecords($conn, 'bnew_machine', $dr_number);
                break;
            case 'usedmachine':
                $used_machines = getRelatedRecords($conn, 'used_machine', $dr_number);
                break;
            case 'drinvoice':
                $dr_invoices = getRelatedRecords($conn, 'dr_invoice', $dr_number);
                break;
            case 'replacementmachine':
                $replacement_machines = getRelatedRecords($conn, 'replacement_machine', $dr_number);
                break;
            case 'drwithprice':
                $dr_with_prices = getRelatedRecords($conn, 'dr_with_price', $dr_number);
                break;
            case 'useddr':
                $used_drs = getRelatedRecords($conn, 'used_dr', $dr_number);
                break;
            case 'pulloutmachine':
                $pullout_machines = getRelatedRecords($conn, 'pullout_machine', $dr_number);
                break;
            case 'pulloutandreplacement':
                $pullout_machines = getRelatedRecords($conn, 'pullout_machine', $dr_number);
                $replacement_machines = getRelatedRecords($conn, 'replacement_machine', $dr_number);
                break;
        }
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

// Get today's count using prepared statement
$today = date('Y-m-d');
$stmt_today = $conn->prepare("SELECT COUNT(*) as count FROM main WHERE DATE(created_at) = ?");
$stmt_today->bind_param("s", $today);
$stmt_today->execute();
$result_today = $stmt_today->get_result();
$today_count = $result_today->fetch_assoc()['count'];
$stmt_today->close();

// Get total bnew machines count
$stmt_bnew = $conn->prepare("SELECT COUNT(*) as count FROM bnew_machine");
$stmt_bnew->execute();
$result_bnew = $stmt_bnew->get_result();
$bnew_count = $result_bnew->fetch_assoc()['count'];
$stmt_bnew->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Invoices Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <script src="./Mainscript.js"></script>
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
                        <input type="text" id="searchInput" placeholder="Search invoices...">
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
                                        <!-- In your table row -->
                                        <td class="action-cell">
                                            <div class="action-buttons">
                                                <button class="btn-action btn-view"
                                                    onclick="viewInvoice(<?php echo $row['id']; ?>)"
                                                    data-id="<?php echo $row['id']; ?>">
                                                    <i class="fas fa-eye"></i>View
                                                </button>
                                                <button class="btn-action btn-edit"
                                                    onclick="editInvoice(<?php echo $row['id']; ?>)"
                                                    data-id="<?php echo $row['id']; ?>">
                                                    <i class="fas fa-edit"></i>Edit
                                                </button>
                                                <button class="btn-action btn-print"
                                                    onclick="window.location.href='print.php?id=<?php echo $row['id']; ?>'">
                                                    <i class="fas fa-print"></i>Print
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
                        Showing <span id="totalRows"><?php echo $total_invoices; ?></span> invoices
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Invoice Modal - DYNAMIC VERSION -->
    <!-- Edit Invoice Modal - FIXED VERSION -->
    <?php if ($edit_id > 0): ?>
        <?php if ($edit_invoice): ?>
            <div class="modal" id="editInvoiceModal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Edit Invoice - <?php echo strtoupper($edit_invoice['type']); ?></h3>
                        <button type="button" class="modal-close" onclick="closeModal('editInvoiceModal')">&times;</button>
                    </div>
                    <form method="POST" action="">
                        <div class="modal-body">

                            <!-- Main Details Section -->
                            <div class="section-header">
                                <h4><i class="fas fa-info-circle"></i> Main Details</h4>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="si_number">SI Number *</label>
                                    <input type="text" name="si_number" class="form-control" value="<?php echo htmlspecialchars($edit_invoice['si_number']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="dr_number">DR Number *</label>
                                    <input type="text" name="dr_number" class="form-control" value="<?php echo htmlspecialchars($edit_invoice['dr_number']); ?>" required readonly>
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

                            <!-- Dynamic Sections Based on Invoice Type -->
                            <?php if ($edit_invoice['type'] == 'bnew' && !empty($bnew_machines)): ?>
                                <div class="section-header">
                                    <h4><i class="fas fa-robot"></i> Brand New Machines</h4>
                                </div>
                                <div id="bnew-section">
                                    <?php foreach ($bnew_machines as $index => $machine): ?>
                                        <div class="dynamic-row" data-index="<?php echo $index; ?>">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Unit Type</label>
                                                    <input type="text" name="bnew_unit_type[]" class="form-control" value="<?php echo htmlspecialchars($machine['unit_type']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Machine Model</label>
                                                    <input type="text" name="bnew_machine_model[]" class="form-control" value="<?php echo htmlspecialchars($machine['machine_model']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Serial No.</label>
                                                    <input type="text" name="bnew_serial_no[]" class="form-control" value="<?php echo htmlspecialchars($machine['serial_no']); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            <?php endif; ?>

                            <?php if ($edit_invoice['type'] == 'usedmachine' && !empty($used_machines)): ?>
                                <div class="section-header">
                                    <h4><i class="fas fa-cogs"></i> Used Machines</h4>
                                </div>
                                <div id="used-section">
                                    <?php foreach ($used_machines as $index => $machine): ?>
                                        <div class="dynamic-row" data-index="<?php echo $index; ?>">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Unit Type</label>
                                                    <input type="text" name="used_unit_type[]" class="form-control" value="<?php echo htmlspecialchars($machine['unit_type']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Machine Model</label>
                                                    <input type="text" name="used_machine_model[]" class="form-control" value="<?php echo htmlspecialchars($machine['machine_model']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Serial No.</label>
                                                    <input type="text" name="used_serial_no[]" class="form-control" value="<?php echo htmlspecialchars($machine['serial_no']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>MR Start</label>
                                                    <input type="text" name="used_mr_start[]" class="form-control" value="<?php echo htmlspecialchars($machine['mr_start']); ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Color Impression</label>
                                                    <input type="text" name="used_color_imp[]" class="form-control" value="<?php echo htmlspecialchars($machine['color_impression']); ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Black Impression</label>
                                                    <input type="text" name="used_black_imp[]" class="form-control" value="<?php echo htmlspecialchars($machine['black_impression']); ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Color Large Impression</label>
                                                    <input type="text" name="used_color_large_imp[]" class="form-control" value="<?php echo htmlspecialchars($machine['color_large_impression']); ?>" oninput="formatPrice(this)">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            <?php endif; ?>

                            <?php if ($edit_invoice['type'] == 'replacementmachine' && !empty($replacement_machines)): ?>
                                <div class="section-header">
                                    <h4><i class="fas fa-exchange-alt"></i> Replacement Machines</h4>
                                </div>
                                <div id="replacement-section">
                                    <?php foreach ($replacement_machines as $index => $machine): ?>
                                        <div class="dynamic-row" data-index="<?php echo $index; ?>">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Unit Type</label>
                                                    <input type="text" name="replace_unit_type[]" class="form-control" value="<?php echo htmlspecialchars($machine['unit_type']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Machine Model</label>
                                                    <input type="text" name="replace_machine_model[]" class="form-control" value="<?php echo htmlspecialchars($machine['machine_model']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Serial No.</label>
                                                    <input type="text" name="replace_serial_no[]" class="form-control" value="<?php echo htmlspecialchars($machine['serial_no']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>MR Start</label>
                                                    <input type="text" name="replace_mr_start[]" class="form-control" value="<?php echo number_format($machine['mr_start']); ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Color Impression</label>
                                                    <input type="text" name="replace_color_imp[]" class="form-control" value="<?php echo number_format($machine['color_impression']); ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Black Impression</label>
                                                    <input type="text" name="replace_black_imp[]" class="form-control" value="<?php echo number_format($machine['black_impression']); ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Color Large Impression</label>
                                                    <input type="text" name="replace_color_large_imp[]" class="form-control" value="<?php echo number_format($machine['color_large_impression']); ?>" oninput="formatPrice(this)">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            <?php endif; ?>

                            <?php if ($edit_invoice['type'] == 'drinvoice' && !empty($dr_invoices)): ?>
                                <div class="section-header">
                                    <h4><i class="fas fa-file-invoice"></i> DR Invoice Items</h4>
                                </div>
                                <div class="form-group">
                                    <label>Delivery Type</label>
                                    <select name="delivery_type" class="form-control">
                                        <option value="partial" <?php echo (isset($dr_invoices[0]['delivery_type']) && $dr_invoices[0]['delivery_type'] == 'partial') ? 'selected' : ''; ?>>Partial</option>
                                        <option value="complete" <?php echo (isset($dr_invoices[0]['delivery_type']) && $dr_invoices[0]['delivery_type'] == 'complete') ? 'selected' : ''; ?>>Complete</option>
                                    </select>
                                </div>
                                <div id="invoice-section">
                                    <?php foreach ($dr_invoices as $index => $invoice): ?>
                                        <div class="dynamic-row" data-index="<?php echo $index; ?>">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Machine Model</label>
                                                    <input type="text" name="invoice_machine_model[]" class="form-control" value="<?php echo htmlspecialchars($invoice['machine_model']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Under P.O No.</label>
                                                    <input type="text" name="invoice_under_po_no[]" class="form-control" value="<?php echo htmlspecialchars($invoice['under_po_no']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Under Invoice No.</label>
                                                    <input type="text" name="invoice_under_invoice_no[]" class="form-control" value="<?php echo htmlspecialchars($invoice['under_invoice_no']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Note</label>
                                                    <input type="text" name="invoice_note[]" class="form-control" value="<?php echo htmlspecialchars($invoice['note']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Quantity</label>
                                                    <input type="text" name="invoice_quantity[]" class="form-control" value="<?php echo isset($invoice['quantity']) ? number_format($invoice['quantity']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Unit Type</label>
                                                    <input type="text" name="invoice_unit_type[]" class="form-control" value="<?php echo htmlspecialchars($invoice['unit_type']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Item Description</label>
                                                    <input type="text" name="invoice_item_desc[]" class="form-control" value="<?php echo htmlspecialchars($invoice['item_description']); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn-add-row" onclick="addRow('invoice')">+ Add Item</button>
                            <?php endif; ?>

                            <?php if ($edit_invoice['type'] == 'drwithprice' && !empty($dr_with_prices)): ?>
                                <div class="section-header">
                                    <h4><i class="fas fa-dollar-sign"></i> DR with Price Items</h4>
                                </div>
                                <div id="price-section">
                                    <?php foreach ($dr_with_prices as $index => $item): ?>
                                        <div class="dynamic-row" data-index="<?php echo $index; ?>">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Machine Model</label>
                                                    <input type="text" name="price_machine_model[]" class="form-control" value="<?php echo htmlspecialchars($item['machine_model']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Quantity</label>
                                                    <input type="text" name="price_quantity[]" class="form-control" value="<?php echo isset($item['quantity']) ? number_format($item['quantity']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Price</label>
                                                    <input type="text" name="price[]" class="form-control" value="<?php echo isset($item['price']) ? number_format($item['price'], 2) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Unit Type</label>
                                                    <input type="text" name="price_unit_type[]" class="form-control" value="<?php echo htmlspecialchars($item['unit_type']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Item Description</label>
                                                    <input type="text" name="price_item_desc[]" class="form-control" value="<?php echo htmlspecialchars($item['item_description']); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn-add-row" onclick="addRow('price')">+ Add Item</button>
                            <?php endif; ?>

                            <?php if ($edit_invoice['type'] == 'useddr' && !empty($used_drs)): ?>
                                <div class="section-header">
                                    <h4><i class="fas fa-tools"></i> Used DR Items</h4>
                                </div>
                                <div id="useddr-section">
                                    <?php foreach ($used_drs as $index => $item): ?>
                                        <div class="dynamic-row" data-index="<?php echo $index; ?>">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Machine Model</label>
                                                    <input type="text" name="useddr_machine_model[]" class="form-control" value="<?php echo htmlspecialchars($item['machine_model']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Serial No.</label>
                                                    <input type="text" name="useddr_serial_no[]" class="form-control" value="<?php echo htmlspecialchars($item['serial_no']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>MR Start</label>
                                                    <input type="text" name="useddr_mr_start[]" class="form-control" value="<?php echo isset($item['mr_start']) ? number_format($item['mr_start']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Technician Name</label>
                                                    <input type="text" name="useddr_technician_name[]" class="form-control" value="<?php echo htmlspecialchars($item['technician_name']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>PR Number</label>
                                                    <input type="text" name="useddr_pr_number[]" class="form-control" value="<?php echo htmlspecialchars($item['pr_number']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Quantity</label>
                                                    <input type="text" name="useddr_quantity[]" class="form-control" value="<?php echo isset($item['quantity']) ? number_format($item['quantity']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Unit Type</label>
                                                    <input type="text" name="useddr_unit_type[]" class="form-control" value="<?php echo htmlspecialchars($item['unit_type']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Item Description</label>
                                                    <input type="text" name="useddr_item_desc[]" class="form-control" value="<?php echo htmlspecialchars($item['item_description']); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn-add-row" onclick="addRow('useddr')">+ Add Item</button>
                            <?php endif; ?>

                            <?php if ($edit_invoice['type'] == 'pulloutmachine' && !empty($pullout_machines)): ?>
                                <div class="section-header">
                                    <h4><i class="fas fa-arrow-circle-left"></i> Pullout Machines</h4>
                                </div>
                                <div id="pullout-section">
                                    <?php foreach ($pullout_machines as $index => $machine): ?>
                                        <div class="dynamic-row" data-index="<?php echo $index; ?>">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Machine Model</label>
                                                    <input type="text" name="pullout_machine_model[]" class="form-control" value="<?php echo htmlspecialchars($machine['machine_model']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Serial No.</label>
                                                    <input type="text" name="pullout_serial_no[]" class="form-control" value="<?php echo htmlspecialchars($machine['serial_no']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>MR End</label>
                                                    <input type="text" name="pullout_mr_end[]" class="form-control" value="<?php echo isset($machine['mr_end']) ? number_format($machine['mr_end']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Color Impression</label>
                                                    <input type="text" name="pullout_color_imp[]" class="form-control" value="<?php echo isset($machine['color_impression']) ? number_format($machine['color_impression']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Black Impression</label>
                                                    <input type="text" name="pullout_black_imp[]" class="form-control" value="<?php echo isset($machine['black_impression']) ? number_format($machine['black_impression']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Color Large Impression</label>
                                                    <input type="text" name="pullout_color_large_imp[]" class="form-control" value="<?php echo isset($machine['color_large_impression']) ? number_format($machine['color_large_impression']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            <?php endif; ?>

                            <?php if ($edit_invoice['type'] == 'pulloutandreplacement' && (!empty($pullout_machines) || !empty($replacement_machines))): ?>
                                <!-- Pullout Machines Section -->
                                <div class="section-header">
                                    <h4><i class="fas fa-arrow-circle-left"></i> Pullout Machines</h4>
                                </div>
                                <div id="pullout-section">
                                    <?php foreach ($pullout_machines as $index => $machine): ?>
                                        <div class="dynamic-row" data-index="<?php echo $index; ?>">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Machine Model</label>
                                                    <input type="text" name="pullout_machine_model[]" class="form-control" value="<?php echo htmlspecialchars($machine['machine_model']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Serial No.</label>
                                                    <input type="text" name="pullout_serial_no[]" class="form-control" value="<?php echo htmlspecialchars($machine['serial_no']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>MR End</label>
                                                    <input type="text" name="pullout_mr_end[]" class="form-control" value="<?php echo isset($machine['mr_end']) ? number_format($machine['mr_end']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Color Impression</label>
                                                    <input type="text" name="pullout_color_imp[]" class="form-control" value="<?php echo isset($machine['color_impression']) ? number_format($machine['color_impression']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Black Impression</label>
                                                    <input type="text" name="pullout_black_imp[]" class="form-control" value="<?php echo isset($machine['black_impression']) ? number_format($machine['black_impression']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Color Large Impression</label>
                                                    <input type="text" name="pullout_color_large_imp[]" class="form-control" value="<?php echo isset($machine['color_large_impression']) ? number_format($machine['color_large_impression']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn-add-row" onclick="addRow('pullout')">+ Add Pullout Machine</button>

                                <!-- Replacement Machines Section -->
                                <div class="section-header" style="margin-top: 30px;">
                                    <h4><i class="fas fa-exchange-alt"></i> Replacement Machines</h4>
                                </div>
                                <div id="replacement-section">
                                    <?php foreach ($replacement_machines as $index => $machine): ?>
                                        <div class="dynamic-row" data-index="<?php echo $index; ?>">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Machine Model</label>
                                                    <input type="text" name="replace_machine_model[]" class="form-control" value="<?php echo htmlspecialchars($machine['machine_model']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Serial No.</label>
                                                    <input type="text" name="replace_serial_no[]" class="form-control" value="<?php echo htmlspecialchars($machine['serial_no']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>MR Start</label>
                                                    <input type="text" name="replace_mr_start[]" class="form-control" value="<?php echo isset($machine['mr_start']) ? number_format($machine['mr_start']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Color Impression</label>
                                                    <input type="text" name="replace_color_imp[]" class="form-control" value="<?php echo isset($machine['color_impression']) ? number_format($machine['color_impression']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Black Impression</label>
                                                    <input type="text" name="replace_black_imp[]" class="form-control" value="<?php echo isset($machine['black_impression']) ? number_format($machine['black_impression']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                                <div class="form-group">
                                                    <label>Color Large Impression</label>
                                                    <input type="text" name="replace_color_large_imp[]" class="form-control" value="<?php echo isset($machine['color_large_impression']) ? number_format($machine['color_large_impression']) : ''; ?>" oninput="formatPrice(this)">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn-add-row" onclick="addRow('replace')">+ Add Replacement Machine</button>
                            <?php endif; ?>

                        </div>
                        <div class="form-actions">
                            <input type="hidden" name="id" value="<?php echo $edit_invoice['id']; ?>">
                            <input type="hidden" name="action" value="update">
                            <button type="button" class="btn btn-secondary" onclick="closeModal('editInvoiceModal')">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Invoice</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Show error if invoice not found -->
            <div class="modal" id="editInvoiceModal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Error</h3>
                        <button type="button" class="modal-close" onclick="closeModal('editInvoiceModal')">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Invoice with ID <?php echo $edit_id; ?> not found.</p>
                        <button type="button" class="btn btn-secondary" onclick="closeModal('editInvoiceModal')">Close</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- View Invoice Modal - FIXED VERSION -->
    <?php if ($view_id > 0): ?>
        <?php if ($view_invoice): ?>
            <div class="modal" id="viewInvoiceModal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>View Invoice</h3>
                        <button class="modal-close" onclick="closeModal('viewInvoiceModal')">&times;</button>
                    </div>
                    <div class="modal-body">
                        <!-- View modal content remains the same... -->
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

                            <!-- Rest of your view modal content... -->
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

                        <!-- Rest of your view sections... -->
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
                                                <td style="padding: 10px;"><?php echo number_format($machine['quantity']); ?></td>
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
                                                <td style="padding: 10px;"><?php echo number_format($machine['quantity']); ?></td>
                                                <td style="padding: 10px;"><?php echo number_format($machine['price'], 2); ?></td>
                                                <td style="padding: 10px;"><?php echo number_format($machine['total'], 2); ?></td>
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
                                                <td style="padding: 10px;"><?php echo number_format($machine['quantity']); ?></td>
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
                                                <td style="padding: 10px;"><?php echo number_format($machine['mr_end']); ?></td>
                                                <td style="padding: 10px;"><?php echo number_format($machine['color_impression']); ?></td>
                                                <td style="padding: 10px;"><?php echo number_format($machine['black_impression']); ?></td>
                                                <td style="padding: 10px;"><?php echo number_format($machine['color_large_impression']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <div class="form-actions">
                            <a href="?edit=<?php echo $view_invoice['id']; ?>" class="btn btn-primary">Edit Invoice</a>
                            <button type="button" class="btn btn-secondary" onclick="closeModal('viewInvoiceModal')">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</body>

</html>