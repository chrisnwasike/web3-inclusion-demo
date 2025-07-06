// SPDX-License-Identifier: MIT
pragma solidity ^0.8.19;

/**
 * @title Inclusion Token (INCL)
 * @dev ERC-20 token for Web3 financial inclusion demo
 * @notice This is a test token for demonstration purposes only
 */
contract InclusionToken {
    string public name = "Inclusion Token";
    string public symbol = "INCL";
    uint8 public decimals = 18;
    uint256 public totalSupply;
    
    mapping(address => uint256) public balanceOf;
    mapping(address => mapping(address => uint256)) public allowance;
    
    // Contract owner and authorized minters
    address public owner;
    mapping(address => bool) public minters;
    
    // Events
    event Transfer(address indexed from, address indexed to, uint256 value);
    event Approval(address indexed owner, address indexed spender, uint256 value);
    event MinterAdded(address indexed minter);
    event MinterRemoved(address indexed minter);
    
    modifier onlyOwner() {
        require(msg.sender == owner, "Not the owner");
        _;
    }
    
    modifier onlyMinter() {
        require(minters[msg.sender] || msg.sender == owner, "Not authorized to mint");
        _;
    }
    
    constructor() {
        owner = msg.sender;
        minters[msg.sender] = true;
        // Initial supply for testing
        _mint(msg.sender, 1000000 * 10**decimals);
    }
    
    /**
     * @dev Add a minter (faucet contracts, staking contracts, etc.)
     */
    function addMinter(address _minter) external onlyOwner {
        minters[_minter] = true;
        emit MinterAdded(_minter);
    }
    
    /**
     * @dev Remove a minter
     */
    function removeMinter(address _minter) external onlyOwner {
        minters[_minter] = false;
        emit MinterRemoved(_minter);
    }
    
    /**
     * @dev Mint tokens (only by authorized minters)
     */
    function mint(address _to, uint256 _amount) external onlyMinter {
        _mint(_to, _amount);
    }
    
    /**
     * @dev Internal mint function
     */
    function _mint(address _to, uint256 _amount) internal {
        require(_to != address(0), "Cannot mint to zero address");
        totalSupply += _amount;
        balanceOf[_to] += _amount;
        emit Transfer(address(0), _to, _amount);
    }
    
    /**
     * @dev Transfer tokens
     */
    function transfer(address _to, uint256 _amount) external returns (bool) {
        require(_to != address(0), "Cannot transfer to zero address");
        require(balanceOf[msg.sender] >= _amount, "Insufficient balance");
        
        balanceOf[msg.sender] -= _amount;
        balanceOf[_to] += _amount;
        
        emit Transfer(msg.sender, _to, _amount);
        return true;
    }
    
    /**
     * @dev Approve spending allowance
     */
    function approve(address _spender, uint256 _amount) external returns (bool) {
        allowance[msg.sender][_spender] = _amount;
        emit Approval(msg.sender, _spender, _amount);
        return true;
    }
    
    /**
     * @dev Transfer from approved allowance
     */
    function transferFrom(address _from, address _to, uint256 _amount) external returns (bool) {
        require(_to != address(0), "Cannot transfer to zero address");
        require(balanceOf[_from] >= _amount, "Insufficient balance");
        require(allowance[_from][msg.sender] >= _amount, "Insufficient allowance");
        
        balanceOf[_from] -= _amount;
        balanceOf[_to] += _amount;
        allowance[_from][msg.sender] -= _amount;
        
        emit Transfer(_from, _to, _amount);
        return true;
    }
    
    /**
     * @dev Burn tokens
     */
    function burn(uint256 _amount) external {
        require(balanceOf[msg.sender] >= _amount, "Insufficient balance");
        balanceOf[msg.sender] -= _amount;
        totalSupply -= _amount;
        emit Transfer(msg.sender, address(0), _amount);
    }
}