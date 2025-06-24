# PhoneAuth Events Reference

This document lists all events fired by the PhoneAuth package for Bagisto. Use these events to extend or customize authentication workflows.

## Dispatched Events

| Event Name                       | When Fired                                 | Payload (array)                |
|----------------------------------|--------------------------------------------|-------------------------------|
| phoneauth.customer.registered    | After a customer is registered             | [$customer]                   |
| phoneauth.otp.generated          | When an OTP is generated                   | [$phone, $otpModel]           |
| phoneauth.otp.sent               | When an OTP is sent (register/login)       | [$phone, $otpModel]           |
| phoneauth.otp.verified           | When an OTP is successfully verified       | [$phone, $customer]           |
| phoneauth.otp.failed             | When OTP verification fails                | [$phone, $otp]                |
| phoneauth.customer.login_attempt | When a login attempt is made               | [$customer]                   |
| phoneauth.customer.logged_out    | When a customer logs out                   | [$customer]                   |
| customer.registration.after      | (Bagisto) After customer registration      | [$customer]                   |
| customer.after.verification      | (Bagisto) After customer phone verification| [$customer]                   |

## Usage Example

You can listen to these events in your service provider or any listener:

```php
Event::listen('phoneauth.otp.verified', function ($phone, $customer) {
    // Custom logic here
});
```

## Notes
- These events allow you to hook into the authentication process for notifications, analytics, or custom business logic.
- You can register listeners in your own service provider or via Bagisto's EventServiceProvider.

---

## Overriding the Auth Guard

- In `config/auth.php`, set the `customer` guard to use your PhoneAuth provider/model.
- In `config/concord.php`, set the customer model to `Webkul\\PhoneAuth\\Models\\Customer`.

## Extending

- Add listeners for any event to customize behavior (e.g., send SMS, log actions).
- Add more listeners in your own ServiceProvider or in `PhoneAuthServiceProvider`.
