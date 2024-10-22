<?php

namespace App\Repositories;

use App\Models\User;
class UserRepository extends BaseRepository
{
    protected $roleMapping = [
        'superAdmin' => 1,
        'admin' => 2,
        'user' => 3,
    ];

    public function __construct()
    {
        $this->model = new User();
    }

    /**
     * Search for users by a given search term.
     *
     * This function utilizes full-text search on user attributes such as
     * name, email, and role. It maps the role name to its corresponding
     * value if applicable and formats the search query.
     *
     * @param string $searchTerm The search term to search for users.
     * @return \Illuminate\Database\Eloquent\Collection The collection of users matching the search criteria.
     */
    public function searchUsers(string $searchTerm)
    {
        $mappedRole = $this->mapRoleNameToValue($searchTerm);

        return $this->model::whereRaw(
            "to_tsvector('english', name || ' ' || email || ' ' || role::text) @@ to_tsquery('english', ?)",
            [$this->formatSearchQuery($searchTerm, $mappedRole)]
        )->get();
    }

    /**
     * Formats a search query to utilize full-text search.
     *
     * @param string $searchTerm The search term to format.
     * @param int|null $mappedRole The role value mapped from the role name, if applicable.
     * @return string The formatted search query.
     */
    private function formatSearchQuery($searchTerm, $mappedRole)
    {
        if ($mappedRole) {
            return implode(' & ', explode(' ', $mappedRole));
        }

        return implode(' & ', explode(' ', $searchTerm));
    }

    /**
     * Maps a role name to its corresponding value if applicable.
     *
     * @param string $roleName The role name to map.
     * @return int|null The mapped role value, or null if the role name is not recognized.
     */
    private function mapRoleNameToValue($roleName)
    {
        return $this->roleMapping[$roleName] ?? null;
    }
}
