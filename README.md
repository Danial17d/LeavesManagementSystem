# Leaves Management System

Managing employee leave shouldn't be a headache , for the employee filling out the request, the manager reviewing it, or the HR team keeping track of balances. This project is an attempt to get that right.

Built with Laravel 12, it covers the full lifecycle of a leave request: from the moment an employee submits it, through every approval step up the management chain, all the way to balance deduction and payroll impact.

---

## What it does

An employee opens the app, picks their leave type, sets the dates, optionally uploads a document, and submits. From there, the system takes over.

It figures out who needs to approve the request based on the org chart, the direct manager first, then their manager, and so on up to the CEO. Each person in the chain gets notified when it's their turn. They can approve, reject, or leave a note explaining their decision. Once everyone signs off, the employee's leave balance is updated automatically.

If the leave is unpaid, a payroll deduction record is created at the same time.

There's also a separate flow for employees who need to be assigned to a department, or want to transfer to a different one , same approval pipeline, just a different kind of request.

---

## Leave types

The system supports eight leave types out of the box, each with its own rules around how many days are allowed, whether it's paid, and whether it eats into the employee's balance:

| Type | Days | Paid | Deducts balance |
|---|---|---|---|
| Annual | 21 | Yes | Yes |
| Sick | 120 | Yes | Yes |
| Marriage | 3 | Yes | Yes |
| Maternity | 90 | Yes | Yes |
| Paternity | 3 | Yes | Yes |
| Hajj | 21 | Yes | Yes |
| Bereavement | 5 | Yes | Yes |
| Unpaid | — | No | No |

---

## Who can do what

There are three roles: **Super Admin**, **Admin**, and **Employee**. Permissions are granular , 30+ of them, covering every action across every module. Roles and permissions are managed through [Spatie Laravel Permission](https://github.com/spatie/laravel-permission).

A regular employee can submit leave requests and structure transfer requests, and that's about it. Admins manage users, departments, and leave types. Super admins have full access to everything.

One special case worth knowing: if you're the CEO (i.e. there's no one above you in the org chart), you can't submit a leave request — because there's nobody to approve it.

---

## How approvals work

When a request comes in, the system walks up the organizational hierarchy and builds an approval chain automatically. There's no manual configuration needed per request.

```
Employee → Manager → Division Head → Department Head → CEO
```

Each step is a record in the database. The request tracks which step it's currently on. If any approver rejects it, the chain stops. If someone cancels a request that was already approved, the system handles reversing the balance too — as long as it was approved less than 3 days ago.

---

## Built with

- **Laravel 12** + PHP 8.2
- **Spatie Laravel Permission** for roles and permissions
- **staudenmeir/laravel-adjacency-list** for the recursive org hierarchy queries
- **Blade + Tailwind CSS + Vite** on the frontend
- **SQLite** by default (easy to swap out)

---

## Getting started

You'll need PHP 8.2+, Composer, and Node.js installed.

```bash
git clone <repository-url>
cd LeavesManagementSystem

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate --seed
npm run build

php artisan serve
---
## Codebase map

If you're digging into the code, here's where things live:

```
app/
├── Enums/          # Business rules live here — LeaveType knows its own days, pay type, etc.
├── Models/         # Eloquent models
├── Observers/      # Side-effects on model events (chain creation, balance init, notifications)
├── Services/       # The heavier logic — ApprovalService, LeaveRequestService, PayRollService
├── Rules/          # Custom validation — IsHasBalance checks the employee actually has days left
└── Http/
    ├── Controllers/
    └── Requests/   # LeaveRequestStore handles the multi-rule validation on submission

resources/views/
├── leave_requests/      # Submit, list, view a leave request
├── leave_approvals/     # What managers see when it's their turn to act
├── structure_requests/  # Department assignment requests
├── structures/          # The org chart views
├── users/               # User management
├── calendar/            # Who's off when
└── components/          # Shared Blade components
```
