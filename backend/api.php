<?php
/**
 * INCL Finance - Backend API
 * Provides additional functionality and analytics for the Web3 financial inclusion app
 * 
 * NOTE: This is a demo backend. In production, implement proper security,
 * input validation, and database operations.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database configuration (SQLite for demo)
$db_file = 'incl_finance.db';

try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables if they don't exist
    createTables($pdo);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Route handling
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'track_user':
        trackUser();
        break;
    case 'log_transaction':
        logTransaction();
        break;
    case 'get_analytics':
        getAnalytics();
        break;
    case 'get_user_stats':
        getUserStats();
        break;
    case 'check_reputation':
        checkReputation();
        break;
    case 'get_leaderboard':
        getLeaderboard();
        break;
    case 'save_feedback':
        saveFeedback();
        break;
    case 'get_network_stats':
        getNetworkStats();
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
}

/**
 * Create database tables
 */
function createTables($pdo) {
    // Users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            wallet_address TEXT UNIQUE NOT NULL,
            first_seen DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_seen DATETIME DEFAULT CURRENT_TIMESTAMP,
            total_transactions INTEGER DEFAULT 0,
            total_volume REAL DEFAULT 0,
            reputation_score INTEGER DEFAULT 0,
            country TEXT DEFAULT 'Nigeria',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Transactions table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS transactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            wallet_address TEXT NOT NULL,
            tx_hash TEXT,
            type TEXT NOT NULL,
            amount REAL DEFAULT 0,
            status TEXT DEFAULT 'pending',
            block_number INTEGER,
            gas_used INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (wallet_address) REFERENCES users (wallet_address)
        )
    ");
    
    // Loans table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS loans (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            wallet_address TEXT NOT NULL,
            loan_amount REAL NOT NULL,
            interest_rate REAL NOT NULL,
            status TEXT DEFAULT 'active',
            issued_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            due_date DATETIME,
            repaid_at DATETIME,
            repaid_amount REAL,
            FOREIGN KEY (wallet_address) REFERENCES users (wallet_address)
        )
    ");
    
    // Feedback table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS feedback (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            wallet_address TEXT,
            rating INTEGER NOT NULL,
            comment TEXT,
            feature TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Analytics events table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS analytics_events (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            wallet_address TEXT,
            event_type TEXT NOT NULL,
            event_data TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
}

/**
 * Track user activity
 */
function trackUser() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $wallet_address = $input['wallet_address'] ?? '';
    
    if (!$wallet_address) {
        http_response_code(400);
        echo json_encode(['error' => 'Wallet address required']);
        return;
    }
    
    try {
        // Insert or update user
        $stmt = $pdo->prepare("
            INSERT INTO users (wallet_address, last_seen) 
            VALUES (?, CURRENT_TIMESTAMP)
            ON CONFLICT(wallet_address) 
            DO UPDATE SET last_seen = CURRENT_TIMESTAMP
        ");
        $stmt->execute([$wallet_address]);
        
        // Log analytics event
        logAnalyticsEvent($wallet_address, 'user_visit', json_encode(['timestamp' => time()]));
        
        echo json_encode(['success' => true, 'message' => 'User tracked']);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to track user']);
    }
}

/**
 * Log transaction
 */
function logTransaction() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $required_fields = ['wallet_address', 'type'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Missing required field: $field"]);
            return;
        }
    }
    
    try {
        // Insert transaction
        $stmt = $pdo->prepare("
            INSERT INTO transactions (wallet_address, tx_hash, type, amount, status, block_number, gas_used)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $input['wallet_address'],
            $input['tx_hash'] ?? null,
            $input['type'],
            $input['amount'] ?? 0,
            $input['status'] ?? 'pending',
            $input['block_number'] ?? null,
            $input['gas_used'] ?? null
        ]);
        
        // Update user stats
        updateUserStats($input['wallet_address'], $input['amount'] ?? 0);
        
        // Log analytics event
        logAnalyticsEvent(
            $input['wallet_address'], 
            'transaction', 
            json_encode([
                'type' => $input['type'],
                'amount' => $input['amount'] ?? 0
            ])
        );
        
        echo json_encode(['success' => true, 'transaction_id' => $pdo->lastInsertId()]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to log transaction']);
    }
}

/**
 * Get analytics data
 */
function getAnalytics() {
    global $pdo;
    
    try {
        // Total users
        $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
        $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
        
        // Total transactions
        $stmt = $pdo->query("SELECT COUNT(*) as total_transactions FROM transactions");
        $total_transactions = $stmt->fetch(PDO::FETCH_ASSOC)['total_transactions'];
        
        // Total volume
        $stmt = $pdo->query("SELECT SUM(amount) as total_volume FROM transactions WHERE status = 'success'");
        $total_volume = $stmt->fetch(PDO::FETCH_ASSOC)['total_volume'] ?? 0;
        
        // Active users (last 24 hours)
        $stmt = $pdo->query("
            SELECT COUNT(*) as active_users 
            FROM users 
            WHERE last_seen >= datetime('now', '-1 day')
        ");
        $active_users = $stmt->fetch(PDO::FETCH_ASSOC)['active_users'];
        
        // Transaction types breakdown
        $stmt = $pdo->query("
            SELECT type, COUNT(*) as count 
            FROM transactions 
            GROUP BY type
        ");
        $transaction_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Daily transaction volume (last 7 days)
        $stmt = $pdo->query("
            SELECT DATE(created_at) as date, COUNT(*) as count, SUM(amount) as volume
            FROM transactions 
            WHERE created_at >= datetime('now', '-7 days')
            GROUP BY DATE(created_at)
            ORDER BY date
        ");
        $daily_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total_users' => (int)$total_users,
                'total_transactions' => (int)$total_transactions,
                'total_volume' => (float)$total_volume,
                'active_users' => (int)$active_users,
                'transaction_types' => $transaction_types,
                'daily_stats' => $daily_stats,
                'last_updated' => date('Y-m-d H:i:s')
            ]
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to get analytics']);
    }
}

/**
 * Get user statistics
 */
function getUserStats() {
    global $pdo;
    
    $wallet_address = $_GET['wallet_address'] ?? '';
    
    if (!$wallet_address) {
        http_response_code(400);
        echo json_encode(['error' => 'Wallet address required']);
        return;
    }
    
    try {
        // User basic info
        $stmt = $pdo->prepare("SELECT * FROM users WHERE wallet_address = ?");
        $stmt->execute([$wallet_address]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo json_encode(['success' => true, 'data' => null]);
            return;
        }
        
        // Transaction history
        $stmt = $pdo->prepare("
            SELECT * FROM transactions 
            WHERE wallet_address = ? 
            ORDER BY created_at DESC 
            LIMIT 10
        ");
        $stmt->execute([$wallet_address]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Loan history
        $stmt = $pdo->prepare("
            SELECT * FROM loans 
            WHERE wallet_address = ? 
            ORDER BY issued_at DESC
        ");
        $stmt->execute([$wallet_address]);
        $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate reputation score
        $reputation_score = calculateReputationScore($wallet_address);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'user' => $user,
                'recent_transactions' => $transactions,
                'loans' => $loans,
                'reputation_score' => $reputation_score,
                'risk_level' => getRiskLevel($reputation_score)
            ]
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to get user stats']);
    }
}

/**
 * Check user reputation
 */
function checkReputation() {
    global $pdo;
    
    $wallet_address = $_GET['wallet_address'] ?? '';
    
    if (!$wallet_address) {
        http_response_code(400);
        echo json_encode(['error' => 'Wallet address required']);
        return;
    }
    
    try {
        $reputation_score = calculateReputationScore($wallet_address);
        $risk_level = getRiskLevel($reputation_score);
        
        // Get loan eligibility
        $loan_eligibility = calculateLoanEligibility($wallet_address, $reputation_score);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'reputation_score' => $reputation_score,
                'risk_level' => $risk_level,
                'loan_eligibility' => $loan_eligibility,
                'timestamp' => time()
            ]
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to check reputation']);
    }
}

/**
 * Get leaderboard
 */
function getLeaderboard() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT 
                wallet_address,
                total_transactions,
                total_volume,
                reputation_score,
                SUBSTR(wallet_address, 1, 6) || '...' || SUBSTR(wallet_address, -4) as display_address
            FROM users 
            WHERE total_transactions > 0
            ORDER BY reputation_score DESC, total_volume DESC
            LIMIT 10
        ");
        $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $leaderboard
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to get leaderboard']);
    }
}

/**
 * Save user feedback
 */
function saveFeedback() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $rating = $input['rating'] ?? 0;
    $comment = $input['comment'] ?? '';
    $feature = $input['feature'] ?? '';
    $wallet_address = $input['wallet_address'] ?? '';
    
    if ($rating < 1 || $rating > 5) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid rating']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO feedback (wallet_address, rating, comment, feature)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$wallet_address, $rating, $comment, $feature]);
        
        echo json_encode(['success' => true, 'message' => 'Feedback saved']);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save feedback']);
    }
}

/**
 * Get network statistics
 */
function getNetworkStats() {
    global $pdo;
    
    try {
        // Total value locked simulation
        $stmt = $pdo->query("
            SELECT SUM(amount) as tvl 
            FROM transactions 
            WHERE type IN ('stake', 'loan') AND status = 'success'
        ");
        $tvl = $stmt->fetch(PDO::FETCH_ASSOC)['tvl'] ?? 0;
        
        // Average transaction size
        $stmt = $pdo->query("
            SELECT AVG(amount) as avg_tx_size 
            FROM transactions 
            WHERE status = 'success' AND amount > 0
        ");
        $avg_tx_size = $stmt->fetch(PDO::FETCH_ASSOC)['avg_tx_size'] ?? 0;
        
        // Success rate
        $stmt = $pdo->query("
            SELECT 
                COUNT(CASE WHEN status = 'success' THEN 1 END) * 100.0 / COUNT(*) as success_rate
            FROM transactions
        ");
        $success_rate = $stmt->fetch(PDO::FETCH_ASSOC)['success_rate'] ?? 0;
        
        // Network growth (new users per day)
        $stmt = $pdo->query("
            SELECT DATE(first_seen) as date, COUNT(*) as new_users
            FROM users 
            WHERE first_seen >= datetime('now', '-30 days')
            GROUP BY DATE(first_seen)
            ORDER BY date
        ");
        $growth_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total_value_locked' => (float)$tvl,
                'average_transaction_size' => (float)$avg_tx_size,
                'success_rate' => (float)$success_rate,
                'network_growth' => $growth_data,
                'last_updated' => date('Y-m-d H:i:s')
            ]
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to get network stats']);
    }
}

/**
 * Helper Functions
 */

function updateUserStats($wallet_address, $amount) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        UPDATE users 
        SET 
            total_transactions = total_transactions + 1,
            total_volume = total_volume + ?,
            last_seen = CURRENT_TIMESTAMP
        WHERE wallet_address = ?
    ");
    $stmt->execute([$amount, $wallet_address]);
}

function logAnalyticsEvent($wallet_address, $event_type, $event_data) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO analytics_events (wallet_address, event_type, event_data)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$wallet_address, $event_type, $event_data]);
}

function calculateReputationScore($wallet_address) {
    global $pdo;
    
    // Base score calculation
    $score = 0;
    
    // Transaction count (1 point per transaction)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as tx_count 
        FROM transactions 
        WHERE wallet_address = ? AND status = 'success'
    ");
    $stmt->execute([$wallet_address]);
    $tx_count = $stmt->fetch(PDO::FETCH_ASSOC)['tx_count'];
    $score += $tx_count;
    
    // Transaction volume (1 point per 100 INCL)
    $stmt = $pdo->prepare("
        SELECT SUM(amount) as total_volume 
        FROM transactions 
        WHERE wallet_address = ? AND status = 'success'
    ");
    $stmt->execute([$wallet_address]);
    $volume = $stmt->fetch(PDO::FETCH_ASSOC)['total_volume'] ?? 0;
    $score += floor($volume / 100);
    
    // Loan repayment history (10 points per successful repayment)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as repaid_loans 
        FROM loans 
        WHERE wallet_address = ? AND status = 'repaid'
    ");
    $stmt->execute([$wallet_address]);
    $repaid_loans = $stmt->fetch(PDO::FETCH_ASSOC)['repaid_loans'];
    $score += $repaid_loans * 10;
    
    // Account age bonus (1 point per day, max 30)
    $stmt = $pdo->prepare("
        SELECT julianday('now') - julianday(first_seen) as account_age 
        FROM users 
        WHERE wallet_address = ?
    ");
    $stmt->execute([$wallet_address]);
    $account_age = $stmt->fetch(PDO::FETCH_ASSOC)['account_age'] ?? 0;
    $score += min(30, floor($account_age));
    
    return max(0, $score);
}

function getRiskLevel($reputation_score) {
    if ($reputation_score >= 50) return 'Low';
    if ($reputation_score >= 20) return 'Medium';
    if ($reputation_score >= 5) return 'High';
    return 'Very High';
}

function calculateLoanEligibility($wallet_address, $reputation_score) {
    global $pdo;
    
    // Check for active loans
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as active_loans 
        FROM loans 
        WHERE wallet_address = ? AND status = 'active'
    ");
    $stmt->execute([$wallet_address]);
    $active_loans = $stmt->fetch(PDO::FETCH_ASSOC)['active_loans'];
    
    if ($active_loans > 0) {
        return [
            'eligible' => false,
            'reason' => 'Active loan exists',
            'max_amount' => 0
        ];
    }
    
    if ($reputation_score < 5) {
        return [
            'eligible' => false,
            'reason' => 'Insufficient reputation',
            'max_amount' => 0
        ];
    }
    
    // Calculate max loan amount based on reputation
    $base_amount = 200; // Base loan amount
    $reputation_multiplier = min(2.0, $reputation_score / 50); // Max 2x multiplier
    $max_amount = $base_amount * $reputation_multiplier;
    
    return [
        'eligible' => true,
        'reason' => 'Eligible for loan',
        'max_amount' => $max_amount,
        'reputation_multiplier' => $reputation_multiplier
    ];
}

?>