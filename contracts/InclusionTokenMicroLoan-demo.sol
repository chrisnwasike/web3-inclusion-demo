// SPDX-License-Identifier: MIT
pragma solidity ^0.8.19;

import "./InclusionToken-demo.sol";

/**
 * @title INCL Microloan Contract
 * @dev Simple microloan system based on reputation scores
 * @notice Users can borrow INCL tokens based on their on-chain activity
 */
contract INCLMicroloan {
    InclusionToken public token;
    address public owner;
    
    // Loan parameters
    uint256 public baseLoanAmount = 200 * 10**18; // 200 INCL base loan
    uint256 public minCreditScore = 1; // Minimum credit score to qualify
    uint256 public loanPeriod = 5 minutes; // Demo: 5 minutes
    uint256 public interestRate = 5; // 5% interest
    uint256 public constant INTEREST_DENOMINATOR = 100;
    
    // Loan structure
    struct Loan {
        uint256 amount;
        uint256 interestAmount;
        uint256 timestamp;
        uint256 dueDate;
        bool repaid;
        bool defaulted;
    }
    
    // User loans and credit scores
    mapping(address => Loan[]) public userLoans;
    mapping(address => uint256) public creditScores;
    mapping(address => uint256) public successfulRepayments;
    mapping(address => uint256) public totalBorrowed;
    
    // Global stats
    uint256 public totalLoansIssued;
    uint256 public totalAmountLent;
    uint256 public totalRepaid;
    uint256 public totalDefaulted;
    
    // Events
    event LoanIssued(address indexed borrower, uint256 amount, uint256 loanIndex, uint256 dueDate);
    event LoanRepaid(address indexed borrower, uint256 loanIndex, uint256 amount);
    event LoanDefaulted(address indexed borrower, uint256 loanIndex);
    event CreditScoreUpdated(address indexed user, uint256 newScore);
    
    modifier onlyOwner() {
        require(msg.sender == owner, "Not the owner");
        _;
    }
    
    constructor(address _tokenAddress) {
        token = InclusionToken(_tokenAddress);
        owner = msg.sender;
    }
    
    /**
     * @dev Calculate credit score based on on-chain activity
     */
    function calculateCreditScore(address _user) public view returns (uint256) {
        // Get user's transaction count (proxy for activity)
        uint256 txCount = _user.balance > 0 ? 1 : 0; // Simplified for demo
        
        // Get successful repayments
        uint256 repayments = successfulRepayments[_user];
        
        // Get token balance (higher balance = better score)
        uint256 tokenBalance = token.balanceOf(_user);
        uint256 balanceScore = tokenBalance / (10**18); // 1 point per INCL token
        
        // Calculate score: base activity + repayments + balance
        uint256 score = txCount + (repayments * 2) + balanceScore;
        
        // Bonus for first-time users
        if (userLoans[_user].length == 0) {
            score += 5;
        }
        
        return score;
    }
    
    /**
     * @dev Update user's credit score
     */
    function updateCreditScore(address _user) public {
        creditScores[_user] = calculateCreditScore(_user);
        emit CreditScoreUpdated(_user, creditScores[_user]);
    }
    
    /**
     * @dev Check if user is eligible for a loan
     */
    function isEligibleForLoan(address _user) public view returns (bool, string memory) {
        uint256 score = calculateCreditScore(_user);
        
        if (score < minCreditScore) {
            return (false, "Credit score too low");
        }
        
        // Check for active loans
        uint256 loanCount = userLoans[_user].length;
        for (uint256 i = 0; i < loanCount; i++) {
            if (!userLoans[_user][i].repaid && !userLoans[_user][i].defaulted) {
                return (false, "Active loan exists");
            }
        }
        
        return (true, "Eligible for loan");
    }
    
    /**
     * @dev Calculate loan amount based on credit score
     */
    function calculateLoanAmount(address _user) public view returns (uint256) {
        uint256 score = calculateCreditScore(_user);
        
        // Base amount + bonus for higher scores
        uint256 amount = baseLoanAmount;
        if (score >= 10) {
            amount += baseLoanAmount / 2; // 50% bonus for high scores
        } else if (score >= 5) {
            amount += baseLoanAmount / 4; // 25% bonus for medium scores
        }
        
        return amount;
    }
    
    /**
     * @dev Request a loan
     */
    function requestLoan() external {
        (bool eligible, string memory reason) = isEligibleForLoan(msg.sender);
        require(eligible, reason);
        
        // Update credit score
        updateCreditScore(msg.sender);
        
        // Calculate loan amount
        uint256 loanAmount = calculateLoanAmount(msg.sender);
        uint256 interest = (loanAmount * interestRate) / INTEREST_DENOMINATOR;
        uint256 dueDate = block.timestamp + loanPeriod;
        
        // Create loan record
        userLoans[msg.sender].push(Loan({
            amount: loanAmount,
            interestAmount: interest,
            timestamp: block.timestamp,
            dueDate: dueDate,
            repaid: false,
            defaulted: false
        }));
        
        // Update stats
        totalLoansIssued++;
        totalAmountLent += loanAmount;
        totalBorrowed[msg.sender] += loanAmount;
        
        // Mint tokens to user
        token.mint(msg.sender, loanAmount);
        
        uint256 loanIndex = userLoans[msg.sender].length - 1;
        emit LoanIssued(msg.sender, loanAmount, loanIndex, dueDate);
    }
    
    /**
     * @dev Repay a loan
     */
    function repayLoan(uint256 _loanIndex) external {
        require(_loanIndex < userLoans[msg.sender].length, "Invalid loan index");
        
        Loan storage loan = userLoans[msg.sender][_loanIndex];
        require(!loan.repaid, "Loan already repaid");
        require(!loan.defaulted, "Loan defaulted");
        
        uint256 repayAmount = loan.amount + loan.interestAmount;
        require(token.balanceOf(msg.sender) >= repayAmount, "Insufficient balance");
        
        // Transfer tokens back to contract
        token.transferFrom(msg.sender, address(this), repayAmount);
        
        // Mark as repaid
        loan.repaid = true;
        
        // Update stats
        successfulRepayments[msg.sender]++;
        totalRepaid += repayAmount;
        
        // Update credit score
        updateCreditScore(msg.sender);
        
        emit LoanRepaid(msg.sender, _loanIndex, repayAmount);
    }
    
    /**
     * @dev Mark loan as defaulted (can be called by anyone after due date)
     */
    function markDefault(address _borrower, uint256 _loanIndex) external {
        require(_loanIndex < userLoans[_borrower].length, "Invalid loan index");
        
        Loan storage loan = userLoans[_borrower][_loanIndex];
        require(!loan.repaid, "Loan already repaid");
        require(!loan.defaulted, "Loan already defaulted");
        require(block.timestamp > loan.dueDate, "Loan not yet due");
        
        // Mark as defaulted
        loan.defaulted = true;
        totalDefaulted += loan.amount;
        
        emit LoanDefaulted(_borrower, _loanIndex);
    }
    
    /**
     * @dev Get user's loan count
     */
    function getUserLoanCount(address _user) external view returns (uint256) {
        return userLoans[_user].length;
    }
    
    /**
     * @dev Get specific loan details
     */
    function getUserLoan(address _user, uint256 _loanIndex) external view returns (
        uint256 amount,
        uint256 interestAmount,
        uint256 timestamp,
        uint256 dueDate,
        bool repaid,
        bool defaulted,
        uint256 timeLeft
    ) {
        require(_loanIndex < userLoans[_user].length, "Invalid loan index");
        
        Loan storage loan = userLoans[_user][_loanIndex];
        amount = loan.amount;
        interestAmount = loan.interestAmount;
        timestamp = loan.timestamp;
        dueDate = loan.dueDate;
        repaid = loan.repaid;
        defaulted = loan.defaulted;
        
        if (block.timestamp >= dueDate) {
            timeLeft = 0;
        } else {
            timeLeft = dueDate - block.timestamp;
        }
    }
    
    /**
     * @dev Get user's loan summary
     */
    function getUserLoanSummary(address _user) external view returns (
        uint256 currentCreditScore,
        uint256 totalLoans,
        uint256 activeLoans,
        uint256 successfulRepaymentCount,
        uint256 totalBorrowedAmount,
        bool eligibleForNewLoan,
        uint256 potentialLoanAmount
    ) {
        currentCreditScore = calculateCreditScore(_user);
        totalLoans = userLoans[_user].length;
        successfulRepaymentCount = successfulRepayments[_user];
        totalBorrowedAmount = totalBorrowed[_user];
        potentialLoanAmount = calculateLoanAmount(_user);
        
        // Count active loans
        activeLoans = 0;
        for (uint256 i = 0; i < totalLoans; i++) {
            if (!userLoans[_user][i].repaid && !userLoans[_user][i].defaulted) {
                activeLoans++;
            }
        }
        
        (eligibleForNewLoan,) = isEligibleForLoan(_user);
    }
    
    /**
     * @dev Get contract stats
     */
    function getContractStats() external view returns (
        uint256 totalLoansCount,
        uint256 totalLentAmount,
        uint256 totalRepaidAmount,
        uint256 totalDefaultedAmount,
        uint256 currentBaseLoanAmount,
        uint256 currentInterestRate
    ) {
        totalLoansCount = totalLoansIssued;
        totalLentAmount = totalAmountLent;
        totalRepaidAmount = totalRepaid;
        totalDefaultedAmount = totalDefaulted;
        currentBaseLoanAmount = baseLoanAmount;
        currentInterestRate = interestRate;
    }
    
    /**
     * @dev Update loan parameters (owner only)
     */
    function updateLoanParameters(
        uint256 _baseLoanAmount,
        uint256 _minCreditScore,
        uint256 _loanPeriod,
        uint256 _interestRate
    ) external onlyOwner {
        baseLoanAmount = _baseLoanAmount;
        minCreditScore = _minCreditScore;
        loanPeriod = _loanPeriod;
        interestRate = _interestRate;
    }
}