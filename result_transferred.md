We need to introduce a new internal flag in the Laravel backend.

IMPORTANT:
- Do NOT refactor business logic.
- Do NOT modify existing API response structure.
- Do NOT change current endpoints behavior.
- Only extend functionality safely.
- Do NOT expose this flag in API resources.

--------------------------------------------------
1) DATABASE CHANGE
--------------------------------------------------

Add a new boolean column to the `athletes` table:

results_transferred BOOLEAN DEFAULT FALSE

Requirements:
- Create a new migration.
- Default value must be FALSE.
- Column must be NOT NULL.
- Ensure backward compatibility with existing data.
- Existing athletes must automatically have results_transferred = false.

--------------------------------------------------
2) UPDATE QUERIES
--------------------------------------------------

Wherever athletes are used in:
- Rankings
- Results listings
- Athlete lists
- Public search endpoints

Add condition:

WHERE results_transferred = FALSE

IMPORTANT:
- Only apply this filter in public-facing queries.
- Do NOT modify admin panel queries.
- Do NOT break existing relationships.
- Do NOT modify eager loading unless required.

--------------------------------------------------
3) DO NOT:
--------------------------------------------------

- Do NOT return results_transferred in API responses.
- Do NOT add it to API Resources.
- Do NOT change validation logic.
- Do NOT alter business logic beyond filtering.

--------------------------------------------------
4) DELIVERABLES
--------------------------------------------------

Provide:
- Migration file
- Updated Eloquent query examples
- Explanation of where filtering was added
- Confirmation that existing functionality remains intact
