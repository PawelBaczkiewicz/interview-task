* $\textsf{\color{#5ec3d5}{The frontend has been developed in a basic manner to ensure fundamental data display and meet the minimal requirements of the task.}}$
* $\textsf{\color{#5ec3d5}{It is necessary to implement components on the frontend later on during refactoring.}}$
* $\textsf{\color{#5ec3d5}{For notifications and event listeners, broadcasting can be implemented to notify the frontend (this was not required by the task).}}$
* $\textsf{\color{#5ec3d5}{Migrations, views, and factories have been redefined according to DDD principles in the InvoiceServiceProvider::class.}}$
* $\textsf{\color{#5ec3d5}{The CRUD functionality has been limited to the requirements defined in the task.}}$

## Invoice Structure:

The invoice should contain the following fields:
* **Invoice ID**: Auto-generated during creation. 
$\textsf{\color{#5ec3d5}{InvoiceData::class, Invoice::class, also applied in InvoiceProductLineData::class and InvoiceProductLine::class }}$
* **Invoice Status**: Possible states include `draft,` `sending,` and `sent-to-client`.
$\textsf{\color{#5ec3d5}{enum StatusEnum - SQLite does not support, MariaDB does}}$
* **Customer Name** 
* **Customer Email** 
* **Invoice Product Lines**, each with:
  * **Product Name**
  * **Quantity**: Integer, must be positive. $\textsf{\color{#5ec3d5}{InvoiceValidator::class}}$
  * **Unit Price**: Integer, must be positive. $\textsf{\color{#5ec3d5}{InvoiceValidator::class}}$
  * **Total Unit Price**: Calculated as Quantity x Unit Price. $\textsf{\color{#5ec3d5}{InvoiceProductLine::getTotalUnitPrice()}}$
* **Total Price**: Sum of all Total Unit Prices.

$\textsf{\color{#5ec3d5}{Invoice::getTotalPrice() calculation on the fly with optimalization for massive request i.e.}}$
$\textsf{\color{#5ec3d5}{for lists where we only one query is done for entire list "Invoice::getTotalPriceAttribute"}}$


## Required Endpoints:

1. **View Invoice**: Retrieve invoice data in the format above.
2. **Create Invoice**: Initialize a new invoice.
3. **Send Invoice**: Handle the sending of an invoice.

## Functional Requirements:

### Invoice Criteria:

* An invoice can only be created in `draft` status. $\textsf{\color{#5ec3d5}{InvoiceData::class and Migrations}}$
* An invoice can be created with empty product lines. $\textsf{\color{#5ec3d5}{InvoiceData::class and InvoiceValidator::class}}$
* An invoice can only be sent if it is in `draft` status. $\textsf{\color{#5ec3d5}{InvoiceService::class}}$
* An invoice can only be marked as `sent-to-client` if its current status is `sending`. $\textsf{\color{#5ec3d5}{InvoiceStatusListener::class}}$
* To be sent, an invoice must contain product lines with both quantity and unit price as positive integers greater than **zero**.
$\textsf{\color{#5ec3d5}{Invoice::hasValidProductLines() - check if not empty, and despite InvoiceValidator::class}}$
$\textsf{\color{#5ec3d5}{constraints it checks product of quantity and unit price to be > 0}}$
### Invoice Sending Workflow:

* **Send an email notification** to the customer using the `NotificationFacade`. $\textsf{\color{#5ec3d5}{InvoiceService::class}}$
  * The email's subject and message may be hardcoded or customized as needed. $\textsf{\color{#5ec3d5}{InvoiceService::class}}$
  * Change the **Invoice Status** to `sending` after sending the notification. $\textsf{\color{#5ec3d5}{InvoiceService::class}}$

### Delivery:

* Upon successful delivery by the Dummy notification provider:
  * The **Notification Module** triggers a `ResourceDeliveredEvent` via webhook. $\textsf{\color{#5ec3d5}{DummyDriver::class}}$
  * The **Invoice Module** listens for and captures this event. $\textsf{\color{#5ec3d5}{InvoiceStatusListener::class}}$
  * The **Invoice Status** is updated from `sending` to `sent-to-client`. $\textsf{\color{#5ec3d5}{InvoiceStatusListener::class}}$
  * **Note**: This transition requires that the invoice is currently in the `sending` status. $\textsf{\color{#5ec3d5}{InvoiceStatusListener::class}}$

## Technical Requirements:

* **Preferred Approach**: Domain-Driven Design (DDD) is preferred for this project. If you have experience with DDD, please feel free to apply this methodology. However, if you are more comfortable with another approach, you may choose an alternative structure. $\textsf{\color{#5ec3d5}{To be honest I followed you and description on how to build projects with DDD. If I have to adapt the company rules I will.}}$
* **Alternative Submission**: If you have a different, comparable project or task that showcases your skills, you may submit that instead of creating this task.
* **Unit Tests**: Core invoice logic should be unit tested. Testing the returned values from endpoints is not required.
$\textsf{\color{#5ec3d5}{InvoiceProductLineTest::class, InvoiceTest::class, InvoiceFacadeTest::class, InvoiceServiceTest::class}}$

* **Documentation**: Candidates are encouraged to document their decisions and reasoning in comments or a README file, explaining why specific implementations or structures were chosen.
$\textsf{\color{#5ec3d5}{The project doesn't contain many inline comments. However, this README provides an overview, }}$
$\textsf{\color{#5ec3d5}{and we can discuss my approach in more detail. Regarding best practices, internal }}$
$\textsf{\color{#5ec3d5}{company guidelines, documentation, design patterns, and standards like PSR-4/12,}}$
$\textsf{\color{#5ec3d5}{we can cover those later so that I can fully adapt to the team's expectations.}}$

    $\textsf{\color{#5ec3d5}{The project complies with PHPCS and PHPStan requirements.}}$

    $\textsf{\color{#5ec3d5}{I am open to feedback and comments on my work. Thank you.}}$

    

## Setup Instructions:

* Start the project by running `./start.sh`.
* To access the container environment, use: `docker compose exec app bash`.
* $\textsf{\color{#5ec3d5}{additionally run: php artisan migrate:fresh --seed}}$
