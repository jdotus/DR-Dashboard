<?php
// ajax_handler.php
require_once 'config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_invoices':
        getInvoices($conn);
        break;
    case 'get_invoice':
        getInvoice($conn);
        break;
    case 'check_new':
        checkNewInvoices($conn);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

function getInvoices($conn)
{
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $search = $_GET['search'] ?? '';
    $offset = ($page - 1) * $limit;

    $response = [];

    // Get total count
    if (!empty($search)) {
        $searchTerm = "%$search%";
        $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM main 
                                    WHERE si_number LIKE ? OR dr_number LIKE ? 
                                    OR delivered_to LIKE ? OR tin LIKE ? 
                                    OR address LIKE ? OR terms LIKE ? 
                                    OR particulars LIKE ?");
        $countStmt->bind_param("sssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    } else {
        $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM main");
    }

    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $response['total'] = $countResult->fetch_assoc()['total'];
    $countStmt->close();

    // Get paginated results
    if (!empty($search)) {
        $stmt = $conn->prepare("SELECT * FROM main 
                               WHERE si_number LIKE ? OR dr_number LIKE ? 
                               OR delivered_to LIKE ? OR tin LIKE ? 
                               OR address LIKE ? OR terms LIKE ? 
                               OR particulars LIKE ? 
                               ORDER BY created_at DESC, id DESC 
                               LIMIT ? OFFSET ?");
        $stmt->bind_param("sssssssii", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
    } else {
        $stmt = $conn->prepare("SELECT * FROM main ORDER BY created_at DESC, id DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $invoices = [];
    while ($row = $result->fetch_assoc()) {
        $invoices[] = $row;
    }

    $response['invoices'] = $invoices;
    $response['page'] = $page;
    $response['limit'] = $limit;
    $response['search'] = $search;

    echo json_encode($response);
    $stmt->close();
}

function getInvoice($conn)
{
    $id = intval($_GET['id'] ?? 0);
    $type = $_GET['type'] ?? 'view';

    if ($id <= 0) {
        echo json_encode(['error' => 'Invalid ID']);
        return;
    }

    // Get main invoice
    $stmt = $conn->prepare("SELECT * FROM main WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $invoice = $result->fetch_assoc();
    $stmt->close();

    if (!$invoice) {
        echo json_encode(['error' => 'Invoice not found']);
        return;
    }

    $dr_number = $invoice['dr_number'];
    $related = [];

    // Get related records based on type
    switch ($invoice['type']) {
        case 'bnew':
            $related['bnew_machines'] = getRelatedRecords($conn, 'bnew_machine', $dr_number);
            break;
        case 'usedmachine':
            $related['used_machines'] = getRelatedRecords($conn, 'used_machine', $dr_number);
            break;
        case 'drinvoice':
            $related['dr_invoices'] = getRelatedRecords($conn, 'dr_invoice', $dr_number);
            break;
        case 'replacementmachine':
            $related['replacement_machines'] = getRelatedRecords($conn, 'replacement_machine', $dr_number);
            break;
        case 'drwithprice':
            $related['dr_with_prices'] = getRelatedRecords($conn, 'dr_with_price', $dr_number);
            break;
        case 'useddr':
            $related['used_drs'] = getRelatedRecords($conn, 'used_dr', $dr_number);
            break;
        case 'pulloutmachine':
            $related['pullout_machines'] = getRelatedRecords($conn, 'pullout_machine', $dr_number);
            break;
        case 'pulloutandreplacement':
            $related['pullout_machines'] = getRelatedRecords($conn, 'pullout_machine', $dr_number);
            $related['replacement_machines'] = getRelatedRecords($conn, 'replacement_machine', $dr_number);
            break;
    }

    echo json_encode([
        'invoice' => $invoice,
        'related' => $related,
        'type' => $type
    ]);
}

function checkNewInvoices($conn)
{
    $lastCheck = $_GET['last_check'] ?? date('Y-m-d H:i:s', strtotime('-1 minute'));

    $stmt = $conn->prepare("SELECT COUNT(*) as count, MAX(created_at) as last_time 
                           FROM main WHERE created_at > ?");
    $stmt->bind_param("s", $lastCheck);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();

    // Get the latest invoices for notification preview
    $newInvoices = [];
    if ($data['count'] > 0) {
        $stmt2 = $conn->prepare("SELECT id, si_number, dr_number, delivered_to 
                                FROM main WHERE created_at > ? 
                                ORDER BY created_at DESC LIMIT 3");
        $stmt2->bind_param("s", $lastCheck);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        while ($row = $result2->fetch_assoc()) {
            $newInvoices[] = $row;
        }
        $stmt2->close();
    }

    echo json_encode([
        'has_new' => $data['count'] > 0,
        'count' => intval($data['count']),
        'last_time' => $data['last_time'] ?? date('Y-m-d H:i:s'),
        'current_time' => date('Y-m-d H:i:s'),
        'new_invoices' => $newInvoices
    ]);
}
