<?php
// Config and authentication
$config = [
    'production' => false,
    'require_auth' => true
];
$rootpath = $_SERVER['DOCUMENT_ROOT'];
require_once($rootpath.'/auth/index.php');

// Get all admin users for assignment dropdown
$adminUsersQuery = "SELECT id, Childname FROM CHMSUsers WHERE Category = 3 ORDER BY Childname";
$adminResult = $conn->query($adminUsersQuery);
$adminUsers = [];

if ($adminResult) {
    while ($row = $adminResult->fetch_assoc()) {
        $adminUsers[] = $row;
    }
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tickets - Admin Dashboard</title>
    <!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap 5 JavaScript Bundle (includes Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
    /* Admin Dashboard Styles */

/* Global styles */
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f8f9fa;
}

/* Sidebar */
.sidebar {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  z-index: 100;
  padding: 48px 0 0;
  box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
  background-color: #343a40;
}

.sidebar-sticky {
  position: relative;
  top: 0;
  height: calc(100vh - 48px);
  padding-top: 0.5rem;
  overflow-x: hidden;
  overflow-y: auto;
}

.sidebar .nav-link {
  font-weight: 500;
  color: #ced4da;
  padding: 0.5rem 1rem;
  margin: 0.2rem 0;
  border-radius: 0.25rem;
}

.sidebar .nav-link:hover {
  color: #fff;
  background-color: rgba(255, 255, 255, 0.1);
}

.sidebar .nav-link.active {
  color: #fff;
  background-color: rgba(255, 255, 255, 0.2);
}

.sidebar .nav-link .bi {
  margin-right: 4px;
}

/* Main content */
main {
  padding-top: 1.5rem;
}

.navbar-brand {
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
  font-size: 1rem;
  background-color: rgba(0, 0, 0, .25);
  box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
}

/* Cards */
.card {
  margin-bottom: 1.5rem;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  border: none;
  border-radius: 0.5rem;
}

.card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid rgba(0, 0, 0, 0.125);
  padding: 0.75rem 1.25rem;
  font-weight: 500;
}

.card-header i {
  margin-right: 0.5rem;
}

/* Tickets table */
.table {
  margin-bottom: 0;
}

.table th {
  font-weight: 600;
  border-top: none;
  background-color: #f8f9fa;
}

.table td {
  vertical-align: middle;
}

/* Ticket priority colors */
.text-success {
  color: #28a745 !important;
}

.text-warning {
  color: #ffc107 !important;
}

.text-danger {
  color: #dc3545 !important;
}

/* Ticket status badges */
.badge {
  padding: 0.5em 0.7em;
  font-weight: 500;
  font-size: 85%;
}

/* Pagination */
.pagination {
  margin-top: 1.5rem;
  margin-bottom: 0;
}

.page-link {
  color: #007bff;
  border: 1px solid #dee2e6;
}

.page-item.active .page-link {
  background-color: #007bff;
  border-color: #007bff;
}

/* Modal styles */
.modal-header {
  border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.modal-footer {
  border-top: 1px solid rgba(0, 0, 0, 0.125);
}

/* Comment section */
.comment-item {
  border-radius: 0.5rem;
  overflow: hidden;
}

.comment-item .card-header {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

/* Alert messages */
.alert {
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
  margin-bottom: 1rem;
  transition: opacity 0.3s ease-in-out;
}

/* Filter form */
.form-label {
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.form-select, .form-control {
  border-radius: 0.25rem;
  padding: 0.375rem 0.75rem;
  border: 1px solid #ced4da;
}

/* Action buttons */
.btn-group {
  display: flex;
  gap: 0.25rem;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

/* Animations */
.spinner-border {
  width: 1.5rem;
  height: 1.5rem;
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
  .sidebar {
    position: static;
    height: auto;
    padding-top: 1rem;
  }
  
  .sidebar-sticky {
    height: auto;
  }
  
  main {
    padding-top: 1rem;
  }
  
  .btn-group {
    flex-wrap: wrap;
  }
}

/* Dark Mode Support (Optional) */
@media (prefers-color-scheme: dark) {
  body {
    background-color: #212529;
    color: #f8f9fa;
  }
  
  .card {
    background-color: #343a40;
    border-color: #495057;
  }
  
  .card-header {
    background-color: #2b3035;
    border-color: #495057;
  }
  
  .table {
    color: #e9ecef;
  }
  
  .table th {
    background-color: #2b3035;
  }
  
  .table td, .table th {
    border-top-color: #495057;
  }
  
  .modal-content {
    background-color: #343a40;
    border-color: #495057;
  }
  
  .form-control, .form-select {
    background-color: #212529;
    border-color: #495057;
    color: #e9ecef;
  }
  
  .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
  }
}
    </style>
</head>
<body>
  <!-- Simple Ticket Summary -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Ticket Summary</h5>
        <span class="badge bg-secondary" id="refresh-counts" style="cursor: pointer;">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </span>
    </div>
    <div class="card-body py-2">
        <div class="row text-center">
            <div class="col-md-2">
                <div class="py-2">
                    <strong>Total:</strong> <span id="total-count" class="badge bg-secondary">0</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="py-2">
                    <strong>Open:</strong> <span id="open-count" class="badge bg-primary">0</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="py-2">
                    <strong>In Progress:</strong> <span id="in-progress-count" class="badge bg-warning text-dark">0</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="py-2">
                    <strong>Pending:</strong> <span id="pending-count" class="badge bg-info">0</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="py-2">
                    <strong>Resolved:</strong> <span id="resolved-count" class="badge bg-success">0</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="py-2">
                    <strong>Closed:</strong> <span id="closed-count" class="badge bg-dark">0</span>
                </div>
            </div>
        </div>
    </div>
</div>


    <div class="container-fluid">
        <div class="row">

            <main class="">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Tickets</h1>
                    
                    <!-- Message display area -->
                    <div id="message-container" class="alert d-none"></div>
                </div>
                
                <!-- Filter Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-funnel"></i> Filter Tickets
                    </div>
                    <div class="card-body">
                        <form id="filter-form" class="row g-3">
                            <div class="col-md-3">
                                <label for="filter-status" class="form-label">Status</label>
                                <select id="filter-status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="Open">Open</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Resolved">Resolved</option>
                                    <option value="Closed">Closed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter-priority" class="form-label">Priority</label>
                                <select id="filter-priority" class="form-select">
                                    <option value="">All Priorities</option>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter-assigned" class="form-label">Assigned To</label>
                                <select id="filter-assigned" class="form-select">
                                    <option value="">All Admins</option>
                                    <?php foreach ($adminUsers as $admin): ?>
                                    <option value="<?= $admin['id'] ?>"><?= htmlspecialchars($admin['Childname']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter-search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="filter-search" placeholder="Search...">
                            </div>
                            <div class="col-md-3">
                                <label for="filter-start-date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="filter-start-date">
                            </div>
                            <div class="col-md-3">
                                <label for="filter-end-date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="filter-end-date">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                                <button type="button" id="clear-filters" class="btn btn-secondary">Clear Filters</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Tickets Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div><i class="bi bi-ticket-perforated"></i> Tickets</div>
                        <div class="loading-indicator spinner-border spinner-border-sm text-primary d-none" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Subject</th>
                                        <th>Des</th>
                                        <th>File</th>
                                        <th>Rec</th>
                                        <th>User</th>
                                        <th>UserId</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th style="display:none;">Assigned To</th>
                                        <th>Created</th>
                                        <th>Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tickets-list">
                                    <tr>
                                        <td colspan="9" class="text-center">Loading tickets...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <nav aria-label="Tickets pagination" id="pagination-container">
                            <ul class="pagination justify-content-center" id="pagination">
                                <!-- Pagination will be generated here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Update Ticket Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="status-form">
                        <input type="hidden" id="status-ticket-id" name="ticketId">
                        <div class="mb-3">
                            <label for="ticket-status" class="form-label">Status</label>
                            <select class="form-select" id="ticket-status" name="status" required>
                                <option value="Open">Open</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Pending">Pending</option>
                                <option value="Resolved">Resolved</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-status">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Assignment Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalLabel">Assign Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assign-form">
                        <input type="hidden" id="assign-ticket-id" name="ticketId">
                        <div class="mb-3">
                            <label for="assign-to" class="form-label">Assign To</label>
                            <select class="form-select" id="assign-to" name="assignedTo" required>
                                <option value="">Select Admin</option>
                                <?php foreach ($adminUsers as $admin): ?>
                                <option value="<?= $admin['id'] ?>"><?= htmlspecialchars($admin['Childname']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-assignment">Assign</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Comment Modal -->
    <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">Ticket Comments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="mb-3">Ticket: <span id="comment-ticket-subject"></span></h6>
                    
                    <!-- Comments List -->
                    <div class="comments-list mb-4">
                        <div class="comments-container" id="comments-container">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading comments...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Add Comment Form -->
                    <form id="comment-form">
                        <input type="hidden" id="comment-ticket-id" name="ticketId">
                        <div class="mb-3">
                            <label for="comment-text" class="form-label">Add Comment</label>
                            <textarea class="form-control" id="comment-text" name="comment" rows="3" required></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Add Comment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Global variables
            let currentPage = 1;
            const itemsPerPage = 100;
            let totalPages = 1;
            let currentFilters = {};
            
            // Initial tickets load
            loadTickets();
            
            // Initial load of ticket counts
    loadTicketCounts();
    
    // Refresh button functionality
    $("#refresh-counts").on("click", function() {
        loadTicketCounts();
    });
    
    // Make filters apply immediately on select change
    $("#filter-status, #filter-priority, #filter-assigned, #filter-start-date, #filter-end-date").on("change", function() {
        currentPage = 1;
        gatherFilters();
        loadTickets();
    });
    
    // Apply search filter with small delay on keyup
    let searchTimeout;
    $("#filter-search").on("keyup", function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            currentPage = 1;
            gatherFilters();
            loadTickets();
        }, 500);
    });
            
            // ===== Event Listeners =====
            
            // Filter form submission
            $("#filter-form").on("submit", function(e) {
                e.preventDefault();
                currentPage = 1;
                gatherFilters();
                loadTickets();
            });
            
            // Clear filters
            $("#clear-filters").on("click", function() {
                $("#filter-form")[0].reset();
                currentPage = 1;
                currentFilters = {};
                loadTickets();
            });
            
            // Status modal update
            $("#save-status").on("click", function() {
                updateTicketStatus();
            });
            
            // Assign modal update
            $("#save-assignment").on("click", function() {
                assignTicket();
            });
            
            // Comment form submission
            $("#comment-form").on("submit", function(e) {
                e.preventDefault();
                addComment();
            });
            
            // ===== Functions =====
            
            /**
             * Gather filter values
             */
            function gatherFilters() {
                currentFilters = {
                    status: $("#filter-status").val(),
                    priority: $("#filter-priority").val(),
                    assignedTo: $("#filter-assigned").val(),
                    search: $("#filter-search").val(),
                    startDate: $("#filter-start-date").val(),
                    endDate: $("#filter-end-date").val()
                };
            }
            
            
            /**
             * Load ticket counts from server
             */
            function loadTicketCounts() {
                $.ajax({
                    url: "server.php",
                    type: "GET",
                    data: {
                        action: 'get_ticket_counts'
                    },
                    headers: {
                        'X-CSRF-Token': '<?php echo $csrf_token; ?>',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $("#total-count").text(response.data.total || 0);
                            $("#open-count").text(response.data.Open || 0);
                            $("#in-progress-count").text(response.data['In Progress'] || 0);
                            $("#pending-count").text(response.data.Pending || 0);
                            $("#resolved-count").text(response.data.Resolved || 0);
                            $("#closed-count").text(response.data.Closed || 0);
                        } else {
                            console.error('Error loading ticket counts:', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading ticket counts:', error);
                    }
                });
            }
            /**
             * Load tickets from server
             */
            function loadTickets() {
                $(".loading-indicator").removeClass("d-none");
                
                // Prepare data
                const data = {
                    action: 'get_admin_tickets',
                    page: currentPage,
                    limit: itemsPerPage,
                    ...currentFilters
                };
                
                $.ajax({
                    url: "server.php",
                    type: "GET",
                    data: data,
                    headers: {
                        'X-CSRF-Token': '<?php echo $csrf_token; ?>',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            renderTickets(response.data);
                            totalPages = Math.ceil(response.total / itemsPerPage);
                            renderPagination();
                        } else {
                            showMessage("error", response.message || "Failed to load tickets");
                            $("#tickets-list").html(`
                                <tr>
                                    <td colspan="9" class="text-center text-danger">Failed to load tickets</td>
                                </tr>
                            `);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching tickets:', error);
                        showMessage("error", "Server error. Please try again later.");
                        $("#tickets-list").html(`
                            <tr>
                                <td colspan="9" class="text-center text-danger">Error loading tickets. Please try again.</td>
                            </tr>
                        `);
                    },
                    complete: function() {
                        $(".loading-indicator").addClass("d-none");
                    }
                });
            }
            
            /**
             * Render tickets in the table
             */
            function renderTickets(tickets) {
                if (tickets.length === 0) {
                    $("#tickets-list").html(`
                        <tr>
                            <td colspan="9" class="text-center">No tickets found</td>
                        </tr>
                    `);
                    return;
                }
                
                let html = '';
                
                tickets.forEach(function(ticket) {
                    // Set appropriate status class
                    let statusClass = '';
                    switch (ticket.issuestatus) {
                        case 'Open':
                            statusClass = 'bg-primary';
                            break;
                        case 'In Progress':
                            statusClass = 'bg-warning text-dark';
                            break;
                        case 'Pending':
                            statusClass = 'bg-secondary';
                            break;
                        case 'Resolved':
                            statusClass = 'bg-success';
                            break;
                        case 'Closed':
                            statusClass = 'bg-dark';
                            break;
                        default:
                            statusClass = 'bg-primary';
                    }
                    
                    // Set appropriate priority class
                    let priorityClass = '';
                    switch (ticket.issuepriority) {
                        case 'Low':
                            priorityClass = 'text-success';
                            break;
                        case 'Medium':
                            priorityClass = 'text-warning';
                            break;
                        case 'High':
                            priorityClass = 'text-danger';
                            break;
                        case 'Critical':
                            priorityClass = 'text-danger fw-bold';
                            break;
                        default:
                            priorityClass = '';
                    }
                    
                    // Format dates
                    const createdDate = new Date(ticket.created);
                    const formattedCreated = createdDate.toLocaleString();
                    
                   const updatedDate = ticket.updated ? new Date(ticket.updated) : null;
                   const formattedUpdated = updatedDate ? updatedDate.toLocaleString() : "-";

                    
                    html += `
                        <tr>
                            <td>${ticket.issueid}</td>
                            <td>${ticket.issuesub}</td>
                            <td>${ticket.issuedes || '-'}</td>
                            <td>
                                ${ticket.issuefile ? 
                                    `<a href="uploads/${ticket.issuefile}" target="_blank" class="text-decoration-none">View File</a>` 
                                    : '-'}
                            </td>
                            <td>
                                ${ticket.audio_recording ? 
                                    `<a href="uploads/audio/${ticket.audio_recording}" target="_blank" class="text-decoration-none">View Audio</a>` 
                                    : '-'}
                            </td>
                            <td>${ticket.username || 'Unknown'}</td>
                            <td>${ticket.userid || 'Unknown'}</td>
                            <td class="${priorityClass}">${ticket.issuepriority}</td>
                            <td><span class="badge ${statusClass}">${ticket.issuestatus}</span></td>
                            <td  style="display:none;">${ticket.assignedTo || 'Unassigned'}</td>
                            <td>${formattedCreated}</td>
                            <td>${formattedUpdated}</td>
                            <td>
                                <div class="btn-group">

                                    <button class="btn btn-sm btn-outline-info update-status" data-id="${ticket.issueid}" data-status="${ticket.issuestatus}">
                                        <i class="bi bi-arrow-repeat"></i> Update
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning assign-ticket" data-id="${ticket.issueid}" data-assigned="${ticket.assigneduserid || ''}">
                                        <i class="bi bi-person-check"></i> Asign
                                    </button>
                                    <button class="btn btn-sm btn-outline-success view-comments" data-id="${ticket.issueid}" data-subject="${ticket.issuesub}">
                                        <i class="bi bi-chat-dots"></i> Comments
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                
                $("#tickets-list").html(html);
                
                
                $(".update-status").on("click", function() {
                    const ticketId = $(this).data("id");
                    const currentStatus = $(this).data("status");
                    openStatusModal(ticketId, currentStatus);
                });
                
                $(".assign-ticket").on("click", function() {
                    const ticketId = $(this).data("id");
                    const currentAssigned = $(this).data("assigned");
                    openAssignModal(ticketId, currentAssigned);
                });
                
                $(".view-comments").on("click", function() {
                    const ticketId = $(this).data("id");
                    const subject = $(this).data("subject");
                    openCommentModal(ticketId, subject);
                });
            }
            
            /**
             * Render pagination controls
             */
            function renderPagination() {
                if (totalPages <= 1) {
                    $("#pagination-container").hide();
                    return;
                }
                
                $("#pagination-container").show();
                let html = '';
                
                // Previous button
                html += `
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                `;
                
                // Page numbers
                const startPage = Math.max(1, currentPage - 2);
                const endPage = Math.min(totalPages, startPage + 4);
                
                for (let i = startPage; i <= endPage; i++) {
                    html += `
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                }
                
                // Next button
                html += `
                    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                `;
                
                $("#pagination").html(html);
                
                // Attach click event to pagination links
                $(".page-link").on("click", function(e) {
                    e.preventDefault();
                    const page = $(this).data("page");
                    if (page >= 1 && page <= totalPages) {
                        currentPage = page;
                        loadTickets();
                    }
                });
            }
            
            /**
             * Open status update modal
             */
            function openStatusModal(ticketId, currentStatus) {
                $("#status-ticket-id").val(ticketId);
                $("#ticket-status").val(currentStatus);
                const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
                statusModal.show();
            }
            
            /**
             * Update ticket status
             */
            function updateTicketStatus() {
                const ticketId = $("#status-ticket-id").val();
                const status = $("#ticket-status").val();
                
                $.ajax({
                    url: "server.php",
                    type: "POST",
                    data: {
                        action: 'update_ticket_status',
                        ticketId: ticketId,
                        status: status
                    },
                   headers: {
                        'X-CSRF-Token': '<?php echo $csrf_token; ?>',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showMessage("success", "Ticket status updated successfully");
                            loadTickets();
                            const statusModal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
                            statusModal.hide();
                        } else {
                            showMessage("error", response.message || "Failed to update ticket status");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating status:', error);
                        showMessage("error", "Server error. Please try again later.");
                    }
                });
            }
            
            /**
             * Open assign ticket modal
             */
            function openAssignModal(ticketId, currentAssigned) {
                $("#assign-ticket-id").val(ticketId);
                $("#assign-to").val(currentAssigned);
                const assignModal = new bootstrap.Modal(document.getElementById('assignModal'));
                assignModal.show();
            }
            
            /**
             * Assign ticket to admin
             */
            function assignTicket() {
                const ticketId = $("#assign-ticket-id").val();
                const assignedTo = $("#assign-to").val();
                
                $.ajax({
                    url: "server.php",
                    type: "POST",
                    data: {
                        action: 'assign_ticket',
                        ticketId: ticketId,
                        assignedTo: assignedTo
                    },
                    headers: {
                        'X-CSRF-Token': '<?php echo $csrf_token; ?>',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showMessage("success", "Ticket assigned successfully");
                            loadTickets();
                            const assignModal = bootstrap.Modal.getInstance(document.getElementById('assignModal'));
                            assignModal.hide();
                        } else {
                            showMessage("error", response.message || "Failed to assign ticket");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error assigning ticket:', error);
                        showMessage("error", "Server error. Please try again later.");
                    }
                });
            }
            
            /**
             * Open comments modal
             */
            function openCommentModal(ticketId, subject) {
                $("#comment-ticket-id").val(ticketId);
                $("#comment-ticket-subject").text(subject);
                $("#comment-text").val('');
                
                // Load comments
                loadComments(ticketId);
                
                const commentModal = new bootstrap.Modal(document.getElementById('commentModal'));
                commentModal.show();
            }
            
            /**
             * Load ticket comments
             */
            function loadComments(ticketId) {
                $("#comments-container").html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading comments...</span>
                        </div>
                    </div>
                `);
                
                $.ajax({
                    url: "server.php",
                    type: "GET",
                    data: {
                        action: 'get_ticket_comments',
                        ticketId: ticketId
                    },
                    headers: {
                        'X-CSRF-Token': '<?php echo $csrf_token; ?>',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            renderComments(response.data);
                        } else {
                            $("#comments-container").html(`
                                <div class="alert alert-info">
                                    No comments found for this ticket
                                </div>
                            `);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading comments:', error);
                        $("#comments-container").html(`
                            <div class="alert alert-danger">
                                Failed to load comments. Please try again.
                            </div>
                        `);
                    }
                });
            }
            
            /**
             * Render comments in the modal
             */
               function renderComments(comments) {
    if (!comments || comments.length === 0) {
        $("#comments-container").html(`
            <div class="alert alert-info">
                No comments yet. Be the first to comment on this ticket.
            </div>
        `);
        return;
    }
    
    let html = '';
    
    comments.forEach(function(comment) {
        const commentDate = new Date(comment.created);
        const formattedDate = commentDate.toLocaleString();
        
        // Determine display name based on commentby field
        let displayName;
        switch(comment.commentby) {
            case 'admin':
                displayName = 'Admin';
                break;
            case 'system':
                displayName = 'System';
                break;
            case 'user':
            default:
                displayName = comment.username || 'Unknown User';
                break;
        }
        
        // Check for attachment and add it to the comment if present
        let attachmentHtml = '';
        if (comment.attachment) {
            attachmentHtml = `
                <div class="mt-3">
                    <h6>Attachment:</h6>
                    <a href="uploads/${comment.attachment}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-paperclip me-1"></i> ${comment.attachment}
                    </a>
                </div>
            `;
        }
        
        html += `
            <div class="comment-item card mb-3">
                <div class="card-header bg-light d-flex justify-content-between">
                    <span><strong>${displayName}</strong></span>
                    <span class="text-muted small">${formattedDate}</span>
                </div>
                <div class="card-body">
                    <p class="card-text">${comment.comment}</p>
                    ${attachmentHtml}
                </div>
            </div>
        `;
    });
    
    $("#comments-container").html(html);
}
            
            /**
             * Add comment to ticket
             */
            function addComment() {
                const ticketId = $("#comment-ticket-id").val();
                const comment = $("#comment-text").val().trim();
                
                if (!comment) {
                    showMessage("error", "Comment cannot be empty");
                    return;
                }
                
                $.ajax({
                    url: "server.php",
                    type: "POST",
                    data: {
                        action: 'add_ticket_comment',
                        ticketId: ticketId,
                        comment: comment
                    },
                    headers: {
                        'X-CSRF-Token': '<?php echo $csrf_token; ?>',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $("#comment-text").val('');
                            loadComments(ticketId);
                        } else {
                            showMessage("error", response.message || "Failed to add comment");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error adding comment:', error);
                        showMessage("error", "Server error. Please try again later.");
                    }
                });
            }
            
            /**
             * Display message to the user
             */
            function showMessage(type, text) {
                const messageContainer = $("#message-container");
                
                // Set appropriate class based on message type
                messageContainer.removeClass("alert-success alert-danger d-none")
                    .addClass(type === "success" ? "alert-success" : "alert-danger");
                
                // Add appropriate icon based on message type
                const icon = type === "success" ? 
                    '<i class="bi bi-check-circle-fill me-2"></i>' : 
                    '<i class="bi bi-exclamation-circle-fill me-2"></i>';
                
                // Set message content
                // Set message content
                messageContainer.html(icon + text);
                
                // Automatically hide message after 5 seconds
                setTimeout(function() {
                    messageContainer.addClass("d-none");
                }, 5000);
            }
        });
    </script>
</body>
</html>