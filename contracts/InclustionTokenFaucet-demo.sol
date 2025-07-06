// SPDX-License-Identifier: MIT
pragma solidity ^0.8.19;

import "./InclusionToken-demo.sol";

/**
 * @title INCL Token Faucet
 * @dev Distributes free INCL tokens with rate limiting
 * @notice Users can claim 100 INCL tokens every hour
 */
contract INCLFaucet {
    InclusionToken public token;
    address public owner;
    
    // Claim amount and cooldown period
    uint256 public claimAmount = 100 * 10**18; // 100 INCL tokens
    uint256 public cooldownPeriod = 1 hours;
    
    // Track last claim time for each address
    mapping(address => uint256) public lastClaimTime;
    
    // Track total claims for analytics
    uint256 public totalClaims;
    mapping(address => uint256) public userClaimCount;
    
    // Events
    event TokensClaimed(address indexed user, uint256 amount, uint256 timestamp);
    event ClaimAmountUpdated(uint256 newAmount);
    event CooldownUpdated(uint256 newCooldown);
    
    modifier onlyOwner() {
        require(msg.sender == owner, "Not the owner");
        _;
    }
    
    constructor(address _tokenAddress) {
        token = InclusionToken(_tokenAddress);
        owner = msg.sender;
    }
    
    /**
     * @dev Claim free INCL tokens
     */
    function claimTokens() external {
        require(canClaim(msg.sender), "Cannot claim yet - please wait for cooldown");
        
        // Update claim time
        lastClaimTime[msg.sender] = block.timestamp;
        
        // Mint tokens to user
        token.mint(msg.sender, claimAmount);
        
        // Update stats
        totalClaims++;
        userClaimCount[msg.sender]++;
        
        emit TokensClaimed(msg.sender, claimAmount, block.timestamp);
    }
    
    /**
     * @dev Check if user can claim tokens
     */
    function canClaim(address _user) public view returns (bool) {
        return block.timestamp >= lastClaimTime[_user] + cooldownPeriod;
    }
    
    /**
     * @dev Get time until next claim is available
     */
    function timeUntilNextClaim(address _user) external view returns (uint256) {
        if (canClaim(_user)) {
            return 0;
        }
        return (lastClaimTime[_user] + cooldownPeriod) - block.timestamp;
    }
    
    /**
     * @dev Get user's claim history
     */
    function getUserStats(address _user) external view returns (
        uint256 claimsCount,
        uint256 lastClaim,
        uint256 nextClaimTime,
        bool canClaimNow
    ) {
        claimsCount = userClaimCount[_user];
        lastClaim = lastClaimTime[_user];
        nextClaimTime = lastClaimTime[_user] + cooldownPeriod;
        canClaimNow = canClaim(_user);
    }
    
    /**
     * @dev Update claim amount (owner only)
     */
    function updateClaimAmount(uint256 _newAmount) external onlyOwner {
        claimAmount = _newAmount;
        emit ClaimAmountUpdated(_newAmount);
    }
    
    /**
     * @dev Update cooldown period (owner only)
     */
    function updateCooldown(uint256 _newCooldown) external onlyOwner {
        cooldownPeriod = _newCooldown;
        emit CooldownUpdated(_newCooldown);
    }
    
    /**
     * @dev Emergency withdrawal (owner only)
     */
    function emergencyWithdraw() external onlyOwner {
        uint256 balance = token.balanceOf(address(this));
        if (balance > 0) {
            token.transfer(owner, balance);
        }
    }
    
    /**
     * @dev Get faucet stats
     */
    function getFaucetStats() external view returns (
        uint256 totalClaimsCount,
        uint256 currentClaimAmount,
        uint256 currentCooldown,
        uint256 faucetBalance
    ) {
        totalClaimsCount = totalClaims;
        currentClaimAmount = claimAmount;
        currentCooldown = cooldownPeriod;
        faucetBalance = token.balanceOf(address(this));
    }
}