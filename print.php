<?php
// print-page.php - Direct Database Fetch Version
// Database configuration
$host = 'localhost';
$dbname = 'final_dr';
$username = 'root';
$password = '';

try {
    // Create connection using mysqli (since you're using mysqli in your code)
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if ID is passed via GET
if (!isset($_GET['id'])) {
    die("No ID specified. Please select a record to print.");
}

$id = intval($_GET['id']);

// Fetch main data from database
$sql = "SELECT * FROM main WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Record not found.");
}

$row = $result->fetch_assoc();

// Main form fields
$si_number = $row['si_number'] ?? '';
$dr_number = $row['dr_number'] ?? '';
$delivered_to = $row['delivered_to'] ?? '';
$date = $row['date'] ?? '';
$address = $row['address'] ?? '';
$terms = $row['terms'] ?? '';
$particulars = $row['particulars'] ?? '';
$tin = $row['tin'] ?? '';
$type = $row['type'];

$dr_with_prices = [];
$bnew_machines = [];
$used_machines = [];
$dr_invoices = [];
$replacement_machines = [];
$pullout_machines = [];
$used_drs = [];

// Fetch related data based on type
if ($type === 'bnew') {
    $stmt_bnew = $conn->prepare("SELECT * FROM bnew_machine WHERE dr_number = ?");
    $stmt_bnew->bind_param("i", $dr_number);
    $stmt_bnew->execute();
    $bnew_result = $stmt_bnew->get_result();
    while ($bnew_row = $bnew_result->fetch_assoc()) {
        $bnew_machines[] = $bnew_row;
    }
    $stmt_bnew->close();
} else if ($type === 'usedmachine') {
    // Get used_machine records for this invoice
    $stmt_usedmachine = $conn->prepare("SELECT * FROM used_machine WHERE dr_number = ?");
    $stmt_usedmachine->bind_param("i", $dr_number);
    $stmt_usedmachine->execute();
    $usedmachine_result = $stmt_usedmachine->get_result();
    while ($used_row = $usedmachine_result->fetch_assoc()) {
        $used_machines[] = $used_row;
    }
    $stmt_usedmachine->close();
} else if ($type === 'replacementmachine') {
    // Get replacement_machine records for this invoice
    $stmt_replacement = $conn->prepare('SELECT * FROM replacement_machine WHERE dr_number = ?');
    $stmt_replacement->bind_param('i', $dr_number);
    $stmt_replacement->execute();
    $replacement_result = $stmt_replacement->get_result();
    while ($replacement_row = $replacement_result->fetch_assoc()) {
        $replacement_machines[] = $replacement_row;
    }
    $stmt_replacement->close();
} else if ($type === 'pulloutmachine') {
    // Get pullout_machine records for this invoice
    $stmt_pullout = $conn->prepare('SELECT * FROM pullout_machine WHERE dr_number = ?');
    $stmt_pullout->bind_param('i', $dr_number);
    $stmt_pullout->execute();
    $pullout_result = $stmt_pullout->get_result();
    while ($pullout_row = $pullout_result->fetch_assoc()) {
        $pullout_machines[] = $pullout_row;
    }
    $stmt_pullout->close();
} else if ($type === 'pulloutandreplacement') {
    // Get Pullout Machine records for this invoice
    $stmt_pullout = $conn->prepare('SELECT * FROM pullout_machine WHERE dr_number = ?');
    $stmt_pullout->bind_param('i', $dr_number);
    $stmt_pullout->execute();
    $pullout_result = $stmt_pullout->get_result();
    while ($pullout_row = $pullout_result->fetch_assoc()) {
        $pullout_machines[] = $pullout_row;
    }
    $stmt_pullout->close();

    // Get Replacement Machine records for this invoice
    $stmt_replacement = $conn->prepare('SELECT * FROM replacement_machine WHERE dr_number = ?');
    $stmt_replacement->bind_param('i', $dr_number);
    $stmt_replacement->execute();
    $replacement_result = $stmt_replacement->get_result();
    while ($replacement_row = $replacement_result->fetch_assoc()) {
        $replacement_machines[] = $replacement_row;
    }
    $stmt_replacement->close();
} else if ($type === 'drwithprice') {
    // Get dr_with_price records for this invoice
    $stmt_drwithprice = $conn->prepare('SELECT * FROM dr_with_price WHERE dr_number = ?');
    $stmt_drwithprice->bind_param('i', $dr_number);
    $stmt_drwithprice->execute();
    $drwithprice_result = $stmt_drwithprice->get_result();
    while ($price_row = $drwithprice_result->fetch_assoc()) {
        $dr_with_prices[] = $price_row;
    }
    $stmt_drwithprice->close();
} else if ($type === 'drinvoice') {
    // Get DR Invoice records for this invoice
    $stmt_drinvoice = $conn->prepare('SELECT * FROM dr_invoice WHERE dr_number = ?');
    $stmt_drinvoice->bind_param('i', $dr_number);
    $stmt_drinvoice->execute();
    $drinvoice_result = $stmt_drinvoice->get_result();
    while ($invoice_row = $drinvoice_result->fetch_assoc()) {
        $dr_invoices[] = $invoice_row;
    }
    $stmt_drinvoice->close();
} else if ($type === 'useddr') {
    // Get used_dr records for this invoice
    $stmt_useddr = $conn->prepare('SELECT * FROM used_dr WHERE dr_number = ?');
    $stmt_useddr->bind_param('i', $dr_number);
    $stmt_useddr->execute();
    $useddr_result = $stmt_useddr->get_result();
    while ($useddr_row = $useddr_result->fetch_assoc()) {
        $used_drs[] = $useddr_row;
    }
    $stmt_useddr->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DR and ST</title>
    <style>
        * {
            font-family: "Arial", sans-serif;
            font-weight: 700;
            font-size: 10px;
            margin: 0;
            padding: 0;
            background: #fff;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        /* Keep your A5 layout size */
        .a5 {
            width: 203.2mm;
            /* Your custom A5-like width */
            height: 139.7mm;
            /* Your A5-like height */
            background: white;
            box-sizing: border-box;
            position: relative;
        }

        .portrait-container {
            width: 138mm;
            margin: 0mm 7mm 0mm 5mm;
            padding-top: 29mm;
            box-sizing: border-box;
            background: #fff;
        }

        /* Tables */
        table {
            border-collapse: collapse;
            margin: 0;
            table-layout: fixed;
        }

        td,
        th {
            border: 1px solid red;
            text-align: center;
            box-sizing: border-box;
            vertical-align: top;
        }

        .header td {
            font-weight: bold;
        }

        /* First table */
        table:first-of-type {
            width: 90mm !important;
        }

        /* Third Table */
        table:last-of-type {
            width: 188.5mm !important;
        }

        /* Column widths */
        .col-si-number,
        .col-si-date,
        .col-terms {
            width: 32mm !important;
        }

        .col-sold-to {
            width: 91.5mm !important;
        }

        .col-particulars {
            padding: 1mm 0 0 0;
            width: 64mm !important;
        }

        .col-price {
            width: 38mm !important;
        }

        .col-quantity {
            width: 17.7mm !important;
        }

        .col-units {
            width: 14.3mm !important;
        }

        .col-description {
            padding: 0 5px;
            width: 156.5mm !important;
        }

        .col-description-price {
            width: 96.5mm !important;
        }

        .col-description-header {
            width: 96.5mm !important;
        }

        .col-price {
            width: 30mm !important;
        }

        .dr-row {
            height: 9.5mm;
        }

        .dr-2nd-row {
            height: 6.7mm !important;
        }

        .dr-2nd-row-header {
            height: 5.5mm !important;
        }

        .text-align {
            text-align: left !important;
        }

        .underline-empty {
            text-decoration: underline;
            text-decoration-style: solid;
            text-decoration-thickness: 1px;
            display: inline-block;
            min-width: 55px;
        }

        .dr-2nd-row-new {
            height: 5.4mm !important;
        }

        /* Print setup — centers A5 content on A4 paper */
        @media print {
            @page {
                size: A4 portrait;
                /* A4 paper */
                margin: 0;
                /* no browser margin */
            }

            html,
            body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
                background: none !important;
            }

            .a5 {
                page-break-after: always;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="a5">
        <div class="portrait-container">
            <table>
                <tr class="dr-row">
                    <td class="col-si-number">
                        <br>
                        <?= htmlspecialchars($si_number) ?>
                    </td>
                    <td class="col-sold-to">
                        <?= htmlspecialchars($delivered_to) ?><br>
                        <?= htmlspecialchars($tin) ?>
                    </td>
                </tr>
                <tr class="dr-row">
                    <td class="col-si-date"><br> <?= htmlspecialchars($date) ?></td>
                    <td class="col-sold-to"> <?= htmlspecialchars($address) ?></td>
                </tr>
                <tr class="dr-row">
                    <td class="col-terms"><br> <?= htmlspecialchars($terms) ?></td>
                    <td class="col-particulars"> <?= htmlspecialchars($particulars) ?></td>
                </tr>
            </table>

            <table>
                <!-- HEADINGS -->
                <tr class="dr-2nd-row-header">
                    <td class="col-quantity"></td> <!-- QUANTITY -->
                    <td class="col-units"></td> <!-- UNIT -->
                    <?php if ($type === 'drwithprice') { ?>
                        <td class="col-description-price"></td> <!-- DESCRIPTION for drWithPrice -->
                        <td class="col-price"></td> <!-- PRICE for drWithPrice -->
                    <?php } else { ?>
                        <td class="col-description"></td> <!-- DESCRIPTION for other formats -->
                    <?php } ?>
                </tr>

                <?php if ($type === 'usedmachine') { ?>
                    <tr class="dr-2nd-row">
                        <td><?= htmlspecialchars(count($used_machines)) ?></td>
                        <td><?= htmlspecialchars($used_machines[0]['unit_type'] ?? '') ?></td>
                        <td class="text-align" style="font-size: 10px">Deliver Machine<br>Model: <?= htmlspecialchars($used_machines[0]['machine_model'] ?? '') ?></td>
                    </tr>

                    <?php
                    foreach ($used_machines as $machine) {
                        $srDisplay = trim($machine['serial_no'] ?? '') !== '' ? htmlspecialchars($machine['serial_no']) : '<span class="underline-empty"></span>';
                        $mrDisplay = trim($machine['mr_start'] ?? '') !== '' ? htmlspecialchars($machine['mr_start']) : '<span class="underline-empty"></span>';

                        $ci = trim((string)($machine['color_impression'] ?? ''));
                        $bi = trim((string)($machine['black_impression'] ?? ''));
                        $cli = trim((string)($machine['color_large_impression'] ?? ''));

                        if ($ci === '' && $bi === '' && $cli === '') {
                            $messageFormat = "Serial No.: " . $srDisplay .  " MR Start: "  . $mrDisplay;
                        } else {
                            $messageFormat = "Serial No.: " . $srDisplay . " MR Start: " . $mrDisplay
                                . " (CI: " . htmlspecialchars($ci) . "; BI: " . htmlspecialchars($bi) . "; CLI: " . htmlspecialchars($cli) . ")";
                        }
                    ?>

                        <tr class="dr-2nd-row-new">
                            <td></td>
                            <td></td>
                            <td class="text-align" style="font-size: 11px; "><?= $messageFormat ?></td>
                        </tr>

                    <?php } ?>
                    <?php for ($j = count($used_machines); $j < 6; $j++) { ?>
                        <tr class="dr-2nd-row-new">
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php } ?>

                <?php } else if ($type == 'bnew') {  ?>
                    <?php
                    $countTableRows = 0;
                    $perRow = 3;

                    if (!empty($bnew_machines)) {
                        foreach ($bnew_machines as $machine) {
                            $serialInput = $machine['serial_no'] ?? '';
                            $serialsClean = array_values(array_filter(array_map('trim', explode(',', $serialInput))));
                            $serialsCount = count($serialsClean);

                            if ($serialsCount === 0) continue;
                    ?>

                            <!-- Machine Header Row -->
                            <tr class="dr-2nd-row">
                                <td><?= htmlspecialchars($serialsCount) ?></td>
                                <td><?= htmlspecialchars($machine['unit_type'] ?? '') ?></td>
                                <td class="text-align" style="font-size: 10px">
                                    Deliver Brand New Machine<br>
                                    Model: <?= htmlspecialchars($machine['dr_number'] ?? '') ?>
                                </td>
                            </tr>
                    <?php
                            $printed = 0;

                            foreach ($serialsClean as $sr) {
                                if ($printed % $perRow == 0) {
                                    if ($printed > 0) echo '</td></tr>';
                                    echo '<tr class="dr-2nd-row-new">
                                            <td></td>
                                            <td></td>
                                            <td class="text-align" style="font-size: 11px;">';
                                    $countTableRows++;
                                }

                                echo 'Serial No.: ' . htmlspecialchars($sr) . str_repeat('&nbsp;', 5);
                                $printed++;

                                if ($printed % $perRow == 0 || $printed == $serialsCount) {
                                    echo '</td></tr>';
                                }
                            }
                        }
                    }

                    for ($s = $countTableRows; $s < 5; $s++) {
                        echo '<tr class="dr-2nd-row-new">
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>';
                    }
                    ?>

                <?php } else if ($type == 'replacementmachine') { ?>

                    <?php if (!empty($replacement_machines)) { ?>
                        <tr class="dr-2nd-row">
                            <td><?= htmlspecialchars(count($replacement_machines)) ?></td>
                            <td><?= htmlspecialchars($replacement_machines[0]['unit_type'] ?? '') ?></td>
                            <td class="text-align" style="font-size: 10px">Deliver Replacement Machine <br>Model: <?= htmlspecialchars($replacement_machines[0]['model'] ?? '') ?></td>
                        </tr>

                        <?php foreach ($replacement_machines as $machine) {
                            $srReplacementDisplay = trim($machine['serial'] ?? '') !== '' ? htmlspecialchars($machine['serial']) : '<span class="underline-empty"></span>';
                            $mrReplacementDisplay = trim($machine['mr_start'] ?? '') !== '' ? htmlspecialchars($machine['mr_start']) : '<span class="underline-empty"></span>';

                            $ciDisplay = trim($machine['color_imp'] ?? '') !== '' ? htmlspecialchars($machine['color_imp']) : '<span class="underline-empty"></span>';
                            $biDisplay = trim($machine['black_imp'] ?? '') !== '' ? htmlspecialchars($machine['black_imp']) : '<span class="underline-empty"></span>';
                            $cliDisplay = trim($machine['color_large_imp'] ?? '') !== '' ? htmlspecialchars($machine['color_large_imp']) : '<span class="underline-empty"></span>';

                            $mrReplacementFormat = "MR Start:" . $mrReplacementDisplay . " (CI:" . $ciDisplay . "; BI:" . $biDisplay . "; CLI:" . $cliDisplay . ")";
                        ?>

                            <tr class="dr-2nd-row">
                                <td></td>
                                <td></td>
                                <td class="text-align">Serial No.: <?= $srReplacementDisplay ?> <?= $mrReplacementFormat ?> </td>
                            </tr>

                        <?php }

                        for ($j = count($replacement_machines); $j < 2; $j++) { ?>
                            <tr class="dr-2nd-row">
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php } ?>

                    <?php } ?>

                <?php } else if ($type == 'pulloutmachine') { ?>

                    <?php if (!empty($pullout_machines)) { ?>
                        <tr class="dr-2nd-row">
                            <td><?= htmlspecialchars(count($pullout_machines)) ?></td>
                            <td><?= htmlspecialchars($pullout_machines[0]['unit_type'] ?? '') ?></td>
                            <td class="text-align" style="font-size: 10px">Pull Out Machine <br>Model: <?= htmlspecialchars($pullout_machines[0]['model'] ?? '') ?></td>
                        </tr>

                        <?php foreach ($pullout_machines as $machine) {
                            $srPulloutDisplay = trim($machine['serial'] ?? '') !== '' ? htmlspecialchars($machine['serial']) : '<span class="underline-empty"></span>';
                            $mrPulloutDisplay = trim($machine['mr_end'] ?? '') !== '' ? htmlspecialchars($machine['mr_end']) : '<span class="underline-empty"></span>';

                            $ciDisplay = trim($machine['color_imp'] ?? '') !== '' ? htmlspecialchars($machine['color_imp']) : '<span class="underline-empty"></span>';
                            $biDisplay = trim($machine['black_imp'] ?? '') !== '' ? htmlspecialchars($machine['black_imp']) : '<span class="underline-empty"></span>';
                            $cliDisplay = trim($machine['color_large_imp'] ?? '') !== '' ? htmlspecialchars($machine['color_large_imp']) : '<span class="underline-empty"></span>';

                            $mrPulloutFormat = "MR End:" . $mrPulloutDisplay . " (CI:" . $ciDisplay . "; BI:" . $biDisplay . "; CLI:" . $cliDisplay . ")";
                        ?>

                            <tr class="dr-2nd-row">
                                <td></td>
                                <td></td>
                                <td class="text-align">Serial No.:<?= $srPulloutDisplay ?> <?= $mrPulloutFormat ?> </td>
                            </tr>
                        <?php }

                        for ($i = count($pullout_machines); $i < 6; $i++) { ?>
                            <tr class="dr-2nd-row">
                                <td></td>
                                <td></td>
                                <td class="text-align"></td>
                            </tr>
                        <?php } ?>

                    <?php } ?>

                <?php } else if ($type == 'pulloutandreplacement') { ?>

                    <!-- Replacement Machines -->
                    <?php if (!empty($replacement_machines)) { ?>
                        <tr class="dr-2nd-row">
                            <td><?= htmlspecialchars(count($replacement_machines)) ?></td>
                            <td><?= htmlspecialchars($replacement_machines[0]['unit_type'] ?? '') ?></td>
                            <td class="text-align" style="font-size: 10px">Deliver Replacement Machine <br>Model: <?= htmlspecialchars($replacement_machines[0]['model'] ?? '') ?></td>
                        </tr>

                        <?php foreach ($replacement_machines as $machine) {
                            $srReplacementDisplay = trim($machine['serial'] ?? '') !== '' ? htmlspecialchars($machine['serial']) : '<span class="underline-empty"></span>';
                            $mrReplacementDisplay = trim($machine['mr_start'] ?? '') !== '' ? htmlspecialchars($machine['mr_start']) : '<span class="underline-empty"></span>';

                            $ciDisplay = trim($machine['color_imp'] ?? '') !== '' ? htmlspecialchars($machine['color_imp']) : '<span class="underline-empty"></span>';
                            $biDisplay = trim($machine['black_imp'] ?? '') !== '' ? htmlspecialchars($machine['black_imp']) : '<span class="underline-empty"></span>';
                            $cliDisplay = trim($machine['color_large_imp'] ?? '') !== '' ? htmlspecialchars($machine['color_large_imp']) : '<span class="underline-empty"></span>';

                            $mrReplacementFormat = "MR Start:" . $mrReplacementDisplay . " (CI:" . $ciDisplay . "; BI:" . $biDisplay . "; CLI:" . $cliDisplay . ")";
                        ?>

                            <tr class="dr-2nd-row">
                                <td></td>
                                <td></td>
                                <td class="text-align">Serial No.: <?= $srReplacementDisplay ?> <?= $mrReplacementFormat ?> </td>
                            </tr>

                        <?php }

                        for ($j = count($replacement_machines); $j < 2; $j++) { ?>
                            <tr class="dr-2nd-row">
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>

                    <!-- Pullout Machines -->
                    <?php if (!empty($pullout_machines)) { ?>
                        <tr class="dr-2nd-row">
                            <td><?= htmlspecialchars(count($pullout_machines)) ?></td>
                            <td><?= htmlspecialchars($pullout_machines[0]['unit_type'] ?? '') ?></td>
                            <td class="text-align" style="font-size: 10px">Pull Out Machine <br>Model: <?= htmlspecialchars($pullout_machines[0]['model'] ?? '') ?></td>
                        </tr>

                        <?php foreach ($pullout_machines as $machine) {
                            $srPulloutDisplay = trim($machine['serial'] ?? '') !== '' ? htmlspecialchars($machine['serial']) : '<span class="underline-empty"></span>';
                            $mrPulloutDisplay = trim($machine['mr_end'] ?? '') !== '' ? htmlspecialchars($machine['mr_end']) : '<span class="underline-empty"></span>';

                            $ciDisplay = trim($machine['color_imp'] ?? '') !== '' ? htmlspecialchars($machine['color_imp']) : '<span class="underline-empty"></span>';
                            $biDisplay = trim($machine['black_imp'] ?? '') !== '' ? htmlspecialchars($machine['black_imp']) : '<span class="underline-empty"></span>';
                            $cliDisplay = trim($machine['color_large_imp'] ?? '') !== '' ? htmlspecialchars($machine['color_large_imp']) : '<span class="underline-empty"></span>';

                            $mrPulloutFormat = "MR End:" . $mrPulloutDisplay . " (CI:" . $ciDisplay . "; BI:" . $biDisplay . "; CLI:" . $cliDisplay . ")";
                        ?>

                            <tr class="dr-2nd-row">
                                <td></td>
                                <td></td>
                                <td class="text-align">Serial No.:<?= $srPulloutDisplay ?> <?= $mrPulloutFormat ?> </td>
                            </tr>
                        <?php }

                        for ($j = count($pullout_machines); $j < 2; $j++) { ?>
                            <tr class="dr-2nd-row">
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>

                <?php } else if ($type === 'drinvoice') { ?>
                    <?php foreach ($dr_invoices as $invoice) { ?>
                        <tr class="dr-2nd-row">
                            <td> <?= htmlspecialchars($invoice['quantity'] ?? '') ?></td>
                            <td><?= htmlspecialchars($invoice['unit_type'] ?? '') ?></td>
                            <td class="text-align"><?= htmlspecialchars($invoice['item_desc'] ?? '') ?></td>
                        </tr>
                    <?php } ?>

                    <?php for ($j = count($dr_invoices); $j < 4; $j++) { ?>
                        <tr class="dr-2nd-row-new">
                            <td></td>
                            <td></td>
                            <td class="text-align"></td>
                        </tr>
                    <?php } ?>

                    <tr class="dr-2nd-row-new">
                        <td></td>
                        <td></td>
                        <td class="text-align">Model: <?= htmlspecialchars($dr_invoices[0]['model'] ?? '') ?> </td>
                    </tr>

                    <?php if (!empty($dr_invoices[0]['po_number']) && !empty($dr_invoices[0]['invoice_number'])) { ?>
                        <tr class="dr-2nd-row-new">
                            <td></td>
                            <td></td>
                            <td class="text-align">Under PO No.: <?= htmlspecialchars($dr_invoices[0]['po_number']) ?></td>
                        </tr>

                        <tr class="dr-2nd-row-new">
                            <td></td>
                            <td></td>
                            <td class="text-align">Under Invoice No: <?= htmlspecialchars($dr_invoices[0]['invoice_number']) ?><br> <span style="font-style: italic;"><?= htmlspecialchars($dr_invoices[0]['note'] ?? ''); ?></span></td>
                        </tr>

                    <?php } else if (empty($dr_invoices[0]['po_number']) && !empty($dr_invoices[0]['invoice_number'])) { ?>
                        <tr class="dr-2nd-row-new">
                            <td></td>
                            <td></td>
                            <td class="text-align">Under Invoice No: <?= htmlspecialchars($dr_invoices[0]['invoice_number']) ?><br> <span style="font-style: italic;"><?= htmlspecialchars($dr_invoices[0]['note'] ?? ''); ?></span></td>
                        </tr>

                        <tr class="dr-2nd-row-new">
                            <td></td>
                            <td></td>
                            <td class="text-align"></td>
                        </tr>

                    <?php } else if (!empty($dr_invoices[0]['po_number']) && empty($dr_invoices[0]['invoice_number'])) { ?>
                        <tr class="dr-2nd-row-new">
                            <td></td>
                            <td></td>
                            <td class="text-align">Under PO No.: <?= htmlspecialchars($dr_invoices[0]['po_number']) ?><br><span style="font-style: italic;"><?= htmlspecialchars($dr_invoices[0]['note'] ?? ''); ?></span></td>
                        </tr>

                        <tr class="dr-2nd-row-new">
                            <td></td>
                            <td></td>
                            <td class="text-align"></td>
                        </tr>

                    <?php } else { ?>
                        <tr class="dr-2nd-row-new">
                            <td></td>
                            <td></td>
                            <td class="text-align"></td>
                        </tr>

                        <tr class="dr-2nd-row-new">
                            <td></td>
                            <td></td>
                            <td class="text-align"></td>
                        </tr>

                        <tr class="dr-2nd-row-new">
                            <td></td>
                            <td></td>
                            <td class="text-align"></td>
                        </tr>

                    <?php } ?>

                <?php } else if ($type == 'drwithprice') { ?>
                    <?php
                    $grandTotal = 0;
                    ?>

                    <?php foreach ($dr_with_prices as $price_item) {
                        $priceVal = isset($price_item['price']) ? floatval(str_replace([',', ' '], '', (string)$price_item['price'])) : 0.0;
                        $qtyVal   = isset($price_item['quantity']) ? floatval(str_replace([',', ' '], '', (string)$price_item['quantity'])) : 0.0;

                        $totalPerItem = $priceVal * $qtyVal;
                        $grandTotal += $totalPerItem;
                    ?>
                        <tr class="dr-2nd-row">
                            <td class="col-quantity"> <?= htmlspecialchars($price_item['quantity']) ?></td>
                            <td class="col-units"><?= htmlspecialchars($price_item['unit_type'] ?? '') ?></td>
                            <td class="col-description-price text-align"><?= htmlspecialchars($price_item['item_desc'] ?? '') ?></td>
                            <td class="col-price"><?= htmlspecialchars(number_format((int)$priceVal)) ?></td>
                            <td class="col-price"><?= htmlspecialchars(number_format((int)$totalPerItem)) ?></td>
                        </tr>
                    <?php } ?>

                    <?php for ($j = count($dr_with_prices); $j < 5; $j++) { ?>
                        <tr class="dr-2nd-row">
                            <td class="col-quantity"> </td>
                            <td class="col-units"></td>
                            <td class="col-description-price text-align"></td>
                            <td class="col-price"></td>
                            <td class="col-price"></td>
                        </tr>
                    <?php } ?>
                    <tr class="dr-2nd-row">
                        <td class="col-quantity"> </td>
                        <td class="col-units"></td>
                        <td class="col-description-price text-align">Machine Model: <?= htmlspecialchars($dr_with_prices[0]['model'] ?? '') ?></td>
                        <td class="col-price"></td>
                        <td class="col-price"></td>
                    </tr>

                    <tr class="dr-2nd-row">
                        <td class="col-quantity"> </td>
                        <td class="col-units"></td>
                        <td class="col-description-price text-align"></td>
                        <td class="col-price">TOTAL: </td>
                        <td class="col-price"><?= htmlspecialchars(number_format((int)$grandTotal)) ?></td>
                    </tr>

                <?php } else if ($type == 'useddr') { ?>

                    <?php foreach ($used_drs as $used_dr) { ?>
                        <tr class="dr-2nd-row-header">
                            <td> <?= htmlspecialchars($used_dr['quantity'] ?? '') ?> </td>
                            <td> <?= htmlspecialchars($used_dr['unit_type'] ?? '') ?> </td>
                            <td class="col-description text-align"> <?= htmlspecialchars($used_dr['item_desc'] ?? '') ?> </td>
                        </tr>
                    <?php } ?>

                    <?php for ($j = count($used_drs); $j < 4; $j++) { ?>
                        <tr class="dr-2nd-row-header">
                            <td> </td>
                            <td> </td>
                            <td class="col-description text-align"> </td>
                        </tr>
                    <?php } ?>

                    <tr class="dr-2nd-row-header">
                        <td></td>
                        <td></td>
                        <td class="col-description text-align">
                            <?= !empty($used_drs[0]['model']) ? "Machine Model: " . htmlspecialchars($used_drs[0]['model']) . "<br>" : "" ?>
                            <?= !empty($used_drs[0]['tech_name']) ? "Technician Name: " . htmlspecialchars($used_drs[0]['tech_name']) . "<br>" : "" ?>
                            <?= !empty($used_drs[0]['serial']) ? "Serial No.: " . htmlspecialchars($used_drs[0]['serial']) . "<br>" : "" ?>
                            <?= !empty($used_drs[0]['mr_start']) ? "MR Start: " . htmlspecialchars($used_drs[0]['mr_start']) . "<br>" : "" ?>
                            <?= !empty($used_drs[0]['pr_number']) ? "PR No.: " . htmlspecialchars($used_drs[0]['pr_number']) : "" ?>
                        </td>
                    </tr>

                <?php } ?>
            </table>
        </div>
    </div>
</body>

<script>
    window.onload = function() {
        window.print();
    }
</script>

</html>