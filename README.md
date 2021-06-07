broadway/sensitive-data
=======================

Helpers for handling sensitive data with Broadway.

![build status](https://github.com/broadway/broadway-sensitive-data/actions/workflows/ci.yml/badge.svg)

## Installation

```
$ composer require broadway/sensitive-data
```

## About
In an Event Sourced environment you may have to deal with sensitive (e.g. personal) data
ending up in your event stream. You could encrypt your event stream or remove sensitive data
from your event stream after a certain amount or time (upcasting). Or you could choose not to
store sensitive data in you event stream altogether. That's where this project helps out.

Imagine the use case where a customer wants to pay an order with a credit card and you're not 
allowed to store the credit card number.

A `PayWithCreditCardCommand` (with credit card number) should lead to a
`PaymentWithCreditCardRequestedEvent` (without the credit card number) but the `Processor` that
handles the event does need to know the credit card number.

This project introduces a `SensitiveDataManager` which can be injected into a `CommandHandler`
to capture the sensitive data from the command and make it available to the `SensitiveDataProcessor`
hereby bypassing the event store.

Pros:
* sensitive data is not stored in your event stream
* no need for encryption or upcasting of your events

Cons:
* handling of sensitive data can only be done once per request

## Example

A detailed example with a test case can be found in the [`examples/`][examples] directory.

[examples]: examples/

## License
This project is licensed under the MIT License - see the LICENSE file for details
