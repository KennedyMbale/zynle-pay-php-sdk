# ZynlePay SDK Examples

This directory contains real-world examples demonstrating how to use the ZynlePay PHP SDK with user interfaces and command-line tools.

## Getting Started

1. **Install dependencies:**

   ```bash
   composer install
   ```

2. **Configure your credentials:**
   Edit the example files and replace the placeholder credentials with your actual ZynlePay credentials:

   - `merchantId`: Your merchant ID
   - `apiId`: Your API ID
   - `apiKey`: Your API key

3. **Run the examples:**

   - **Web UI Examples:** Start a local server:

     ```bash
     php -S localhost:8000 -t examples/
     ```

     Then open `http://localhost:8000` in your browser.

   - **CLI Examples:** Run from command line:
     ```bash
     php examples/cli-example.php [command]
     ```

## Examples

### 1. Web UI Examples (`index.php`)

A comprehensive web interface demonstrating all SDK features:

- **MOMO Payments:** Process mobile money payments with a simple form
- **Card Payments:** Handle credit/debit card payments with PCI-compliant forms
- **Payment Status:** Check the status of any transaction
- **Account Balance:** View your account balance

**Features:**

- Tabbed interface for easy navigation
- Form validation and error handling
- Real-time results display
- Responsive design

### 2. Webhook Handler (`webhook-handler.php`)

Demonstrates how to handle payment confirmation webhooks:

- **Webhook Processing:** Validates and processes incoming webhook data
- **Error Handling:** Proper error responses for failed webhooks
- **Simulator:** Test webhook handling with sample data
- **Logging:** Comprehensive logging for debugging

**Use Cases:**

- Payment confirmation handling
- Order status updates
- Automated fulfillment systems

### 3. CLI Examples (`cli-example.php`)

Command-line interface for SDK operations:

- **MOMO Payments:** Process payments from terminal
- **Card Payments:** Handle card transactions via CLI
- **Status Checks:** Query payment status
- **Balance Inquiry:** Check account balance

**Features:**

- Environment variable configuration
- Command-line argument parsing
- JSON output for easy integration
- Error handling and validation

## Environment Variables

For CLI examples, you can set these environment variables:

```bash
export ZYNLEPAY_MERCHANT_ID="your_merchant_id"
export ZYNLEPAY_API_ID="your_api_id"
export ZYNLEPAY_API_KEY="your_api_key"
export ZYNLEPAY_CHANNEL="momo"  # or "card", "bank", etc.
export ZYNLEPAY_SERVICE_ID="1002"
export ZYNLEPAY_SANDBOX="true"  # or "false" for production
```

## Security Notes

⚠️ **Important:** These examples are for demonstration purposes only.

- Never commit real credentials to version control
- Use HTTPS in production
- Validate all input data
- Implement proper authentication for webhook endpoints
- Store sensitive data securely

## Testing

All examples work with both sandbox and production environments. The SDK defaults to sandbox mode for safety.

To test with real payments, set `sandbox: false` in the Client constructor and use production credentials.

## Support

For questions about these examples:

1. Check the main [README](../README.md) for SDK documentation
2. Review the [API documentation](https://sandbox.zynlepay.com/api/docs)
3. Check the test files in `../tests/` for additional usage examples

## License

These examples are released under the same MIT License as the main SDK.
