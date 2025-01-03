# IFM Refund System

The **IFM Refund System** is a web-based application designed to streamline and automate the refund process at the Institute of Finance Management (IFM). It offers distinct roles for accountants, financial officers, and students, ensuring a seamless experience for all stakeholders.

## Screenshot

![Homepage](images_readme/refund-2.png)

*(Above: A preview of the Refund Management System.)*

---

## Features

### 1. **Authentication**
   - **Student**: Students can securely log in to submit refund requests, view their refund history, and track their refund status.
   - **Financial Officer**: Financial officers can authenticate, view refund applications, approve or reject requests, and manage refund workflows.
   - **Accountant**: Accountants can access the system to validate, approve, or disapprove refund requests.
## Screenshot

![Authentication](images_readme/refund-5.png)

*(Above: Authentication System.)	

### 2. **Refund Application**
   - Students can easily submit refund applications by providing necessary details.
   - Applications are processed by financial officers and accountants as part of the approval workflow.

### 3. **Viewing Refund History**
   - Students can view their complete refund application history, including statuses and any remarks added by officers.
   - Financial officers and accountants can access historical data for auditing and reporting purposes.

### 4. **Approval Workflow**
   - The system features a multi-step approval process:
     1. **Student submits refund request**.
     2. **Financial officer reviews** the request and either approves or rejects it.
     3. **Accountant** validates the request and finalizes the approval.
   - Each role is notified of updates via email and in-system notifications.

### 5. **Report Generation**
   - The system can generate detailed reports for each role:
     - Financial officers can generate reports on refund status, pending applications, and approved/refused requests.
     - Students can download reports for their own applications.
     - Accountants can create financial reports based on refund transactions and auditing.

---

## Tech Stack
- **Backend**: PHP
- **Frontend**: HTML, CSS, JavaScript
- **Database**: MySQL
- **Local Development Environment**: XAMPP

---

## Installation and Setup
Follow these steps to set up the project on your local machine:

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/TariqNew/IFM-REFUND-SYSTEM.git
   cd IFM-REFUND-SYSTEM
