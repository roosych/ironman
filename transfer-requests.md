IMPORTANT:
Respond in Russian.
All explanations, comments and descriptions must be in Russian.

We need to implement a full result transfer request workflow in Laravel.

DO NOT break existing business logic.
DO NOT change current API responses unless necessary.
All critical DB operations must use transactions.
Ensure race-condition protection.
Follow clean architecture (service layer).
Prevent duplicate transfers.

====================================================
1) FEATURE OVERVIEW
====================================================

User flow:
- User clicks "Attach Results".
- Backend returns athletes where results_transferred = false.
- User selects athlete and submits transfer request.
- Each user can have ONLY ONE active (pending) request.
- Admin approves or rejects.
- On approval:
    - Duplicate donor athlete results to target athlete.
    - Donor athlete becomes results_transferred = true.
    - Donor athlete disappears from rankings and public lists.

====================================================
2) DATABASE
====================================================

Create table: result_transfer_requests

Columns:
- id
- user_id (FK users)
- source_athlete_id (FK athletes)
- status ENUM: pending, approved, rejected
- reviewed_by (nullable FK users)
- reviewed_at (nullable timestamp)
- comment (nullable text)
- timestamps

Constraints:
- One pending request per user.
- One pending request per athlete.
- Proper indexes and foreign keys.

====================================================
3) ENUM CLASS
====================================================

Create backed enum ResultTransferStatus:
- PENDING
- APPROVED
- REJECTED

Methods:
- label() → localized label
- color() → status color
- isPending()
- isApproved()
- isRejected()

Use Laravel localization.

====================================================
4) USER FLAGS
====================================================

Add to User model:
- hasPendingTransferRequest(): bool
- hasApprovedTransferRequest(): bool

If user has approved request → block new submissions.

====================================================
5) REQUEST CREATION
====================================================

Validate:
- No existing pending request.
- No approved request.
- Athlete exists.
- Athlete.results_transferred = false.
- Athlete has no pending request.
- Prevent duplicates.

====================================================
6) ADMIN APPROVAL (TRANSACTION REQUIRED)
====================================================

Inside DB::transaction:

1. Re-check status pending.
2. Lock donor athlete row.
3. Duplicate results safely (avoid duplicates).
4. Update donor athlete:
   results_transferred = true
5. Update request:
   status = approved
   reviewed_by
   reviewed_at

Reject:
- Only update status, reviewed_by, reviewed_at.

====================================================
7) SECURITY
====================================================

- Use Policies.
- Prevent mass assignment.
- Prevent direct status modification.
- Protect against race conditions.
- Log approvals.

====================================================
8) API ENDPOINTS
====================================================

User:
- GET /api/v1/transfer/eligible-athletes
- POST /api/v1/transfer/request

Admin:
- GET /api/v1/admin/transfer-requests
- POST /api/v1/admin/transfer-requests/{id}/approve
- POST /api/v1/admin/transfer-requests/{id}/reject

====================================================
9) OUTPUT REQUIRED
====================================================

Generate:
- Migration
- Model
- Enum
- Service
- Controller
- Policy
- Transactional approval example
- Explanation of data integrity protection