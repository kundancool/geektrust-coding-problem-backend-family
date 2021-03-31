<?php
/**
 * Meet The Family.
 *
 * @doc https://www.geektrust.in/coding-problem/backend/family
 *
 * @author Kundan <kundandeveloper@gmail.com>
 */

/**
 * Family.
 */
class Family
{
    /**
     * Stored family details.
     *
     * @var array
     */
    protected $family = [];

    /**
     * summary.
     */
    public function __construct()
    {
        if (is_writable(__DIR__)) {
            define('FAMILY_STORE', __DIR__.'/family.json');
        } else {
            define('FAMILY_STORE', '/tmp/family.json');
        }

        // create store if doesn't exist
        if (false == file_exists(FAMILY_STORE)) {
            $this->updateStore();
        }
        $this->family = json_decode(file_get_contents(FAMILY_STORE), true);
        if (empty($this->family)) {
            $this->family = [];
        }
    }

    /**
     * Update data in store.
     */
    private function updateStore()
    {
        file_put_contents(FAMILY_STORE, json_encode($this->family, JSON_PRETTY_PRINT));
    }

    /**
     * Find relationship in family.
     *
     * @param string $name         Name of person
     * @param string $relationship Name of relationship
     *
     * @return array Names of people with that relationship to person
     */
    private function findRelationship(string $name, string $relationship)
    {
        $index = array_search($name, array_column($this->family, 'name'));
        if (false === $index) {
            return "PERSON_NOT_FOUND\n";
        }

        $names = [];
        switch ($relationship) {
            case 'Paternal-Uncle':
                $parentIndex = $this->findParentIndex($name);
                // If parent exists and is Male
                if (false == is_null($parentIndex) && 'Male' == $this->family[$parentIndex]['gender']) {
                    $grandParentIndex = $this->findParentIndex($this->family[$parentIndex]['name']);
                    if (false == is_null($grandParentIndex)) {
                        // Remove parent from siblings
                        $parentalSiblingIds = array_diff($this->family[$grandParentIndex]['children'], [$this->family[$parentIndex]['id']]);
                        foreach ($parentalSiblingIds as $parentalSiblingId) {
                            $parentalSiblingIndex = array_search($parentalSiblingId, array_column($this->family, 'id'));
                            // Only Male Siblings
                            if ('Male' == $this->family[$parentalSiblingIndex]['gender']) {
                                $names[] = $this->family[$parentalSiblingIndex]['name'];
                            }
                        }
                    }
                }
            break;
            case 'Maternal-Uncle':
                $parentIndex = $this->findParentIndex($name);
                // If parent exists and is Male
                if (false == is_null($parentIndex) && 'Female' == $this->family[$parentIndex]['gender']) {
                    $grandParentIndex = $this->findParentIndex($this->family[$parentIndex]['name']);
                    if (false == is_null($grandParentIndex)) {
                        // Remove parent from siblings
                        $parentalSiblingIds = array_diff($this->family[$grandParentIndex]['children'], [$this->family[$parentIndex]['id']]);
                        foreach ($parentalSiblingIds as $parentalSiblingId) {
                            $parentalSiblingIndex = array_search($parentalSiblingId, array_column($this->family, 'id'));
                            // Only Male Siblings
                            if ('Male' == $this->family[$parentalSiblingIndex]['gender']) {
                                $names[] = $this->family[$parentalSiblingIndex]['name'];
                            }
                        }
                    }
                }
            break;
            case 'Paternal-Aunt':
                $parentIndex = $this->findParentIndex($name);
                // If parent exists and is Male
                if (false == is_null($parentIndex) && 'Male' == $this->family[$parentIndex]['gender']) {
                    $grandParentIndex = $this->findParentIndex($this->family[$parentIndex]['name']);
                    if (false == is_null($grandParentIndex)) {
                        // Remove parent from siblings
                        $parentalSiblingIds = array_diff($this->family[$grandParentIndex]['children'], [$this->family[$parentIndex]['id']]);
                        foreach ($parentalSiblingIds as $parentalSiblingId) {
                            $parentalSiblingIndex = array_search($parentalSiblingId, array_column($this->family, 'id'));
                            // Only Female Siblings
                            if ('Female' == $this->family[$parentalSiblingIndex]['gender']) {
                                $names[] = $this->family[$parentalSiblingIndex]['name'];
                            }
                        }
                    }
                }
            break;
            case 'Maternal-Aunt':
                $parentIndex = $this->findParentIndex($name);
                // If parent exists and is Female
                if (false == is_null($parentIndex) && 'Female' == $this->family[$parentIndex]['gender']) {
                    $grandParentIndex = $this->findParentIndex($this->family[$parentIndex]['name']);
                    if (false == is_null($grandParentIndex)) {
                        // Remove parent from siblings
                        $parentalSiblingIds = array_diff($this->family[$grandParentIndex]['children'], [$this->family[$parentIndex]['id']]);
                        foreach ($parentalSiblingIds as $parentalSiblingId) {
                            $parentalSiblingIndex = array_search($parentalSiblingId, array_column($this->family, 'id'));
                            // Only Female Siblings
                            if ('Female' == $this->family[$parentalSiblingIndex]['gender']) {
                                $names[] = $this->family[$parentalSiblingIndex]['name'];
                            }
                        }
                    }
                }
            break;
            case 'Sister-In-Law':
                $spouseIndex = array_search($this->family[$index]['spouse'], array_column($this->family, 'id'));
                $spouseParentIndex = $this->findParentIndex($this->family[$spouseIndex]['name']);
                if (false == is_null($spouseParentIndex) && false == is_null($spouseIndex)) {
                    // Remove parent from siblings
                    $spouseSiblingIds = array_diff($this->family[$spouseParentIndex]['children'], [$this->family[$spouseIndex]['id']]);
                    foreach ($spouseSiblingIds as $spouseSiblingId) {
                        $spouseSiblingIndex = array_search($spouseSiblingId, array_column($this->family, 'id'));
                        // Only Female Siblings
                        if ('Female' == $this->family[$spouseSiblingIndex]['gender']) {
                            $names[] = $this->family[$spouseSiblingIndex]['name'];
                        }
                    }
                }
                if ('Female' == $this->family[$index]['gender']) {
                    $parentIndex = $this->findParentIndex($name);
                    if (false == is_null($parentIndex)) {
                        // Remove parent from siblings
                        $siblingIds = array_diff($this->family[$parentIndex]['children'], [$this->family[$index]['id']]);
                        foreach ($siblingIds as $siblingId) {
                            $siblingIndex = array_search($siblingId, array_column($this->family, 'id'));
                            if ('Male' == $this->family[$siblingIndex]['gender']) {
                                $spouseIndex = array_search($this->family[$siblingIndex]['spouse'], array_column($this->family, 'id'));
                                $names[] = $this->family[$spouseIndex]['name'];
                            }
                        }
                    }
                }
            break;
            case 'Brother-In-Law':
                $spouseIndex = array_search($this->family[$index]['spouse'], array_column($this->family, 'id'));
                $spouseParentIndex = $this->findParentIndex($this->family[$spouseIndex]['name']);
                if (false == is_null($spouseParentIndex) && false == is_null($spouseIndex)) {
                    // Remove parent from siblings
                    $spouseSiblingIds = array_diff($this->family[$spouseParentIndex]['children'], [$this->family[$spouseIndex]['id']]);
                    foreach ($spouseSiblingIds as $spouseSiblingId) {
                        $spouseSiblingIndex = array_search($spouseSiblingId, array_column($this->family, 'id'));
                        // Only Male Siblings
                        if ('Male' == $this->family[$spouseSiblingIndex]['gender']) {
                            $names[] = $this->family[$spouseSiblingIndex]['name'];
                        }
                    }
                }
                if ('Male' == $this->family[$index]['gender']) {
                    $parentIndex = $this->findParentIndex($name);
                    if (false == is_null($parentIndex)) {
                        // Remove parent from siblings
                        $siblingIds = array_diff($this->family[$parentIndex]['children'], [$this->family[$index]['id']]);
                        foreach ($siblingIds as $siblingId) {
                            $siblingIndex = array_search($siblingId, array_column($this->family, 'id'));
                            if ('Female' == $this->family[$siblingIndex]['gender']) {
                                $spouseIndex = array_search($this->family[$siblingIndex]['spouse'], array_column($this->family, 'id'));
                                $names[] = $this->family[$spouseIndex]['name'];
                            }
                        }
                    }
                }
            break;
            case 'Son':
                if (false == empty($this->family[$index]['children'])) {
                    foreach ($this->family[$index]['children'] as $childId) {
                        $childIndex = array_search($childId, array_column($this->family, 'id'));
                        if ('Male' == $this->family[$childIndex]['gender']) {
                            $names[] = $this->family[$childIndex]['name'];
                        }
                    }
                }

            break;
            case 'Daughter':
                if (false == empty($this->family[$index]['children'])) {
                    foreach ($this->family[$index]['children'] as $childId) {
                        $childIndex = array_search($childId, array_column($this->family, 'id'));
                        if ('Female' == $this->family[$childIndex]['gender']) {
                            $names[] = $this->family[$childIndex]['name'];
                        }
                    }
                }

            break;
            case 'Siblings':
                $parentIndex = $this->findParentIndex($name);
                if (false == is_null($parentIndex)) {
                    // Remove parent from siblings
                    $siblingIds = array_diff($this->family[$parentIndex]['children'], [$this->family[$index]['id']]);
                    foreach ($siblingIds as $siblingId) {
                        $siblingIndex = array_search($siblingId, array_column($this->family, 'id'));
                        $names[] = $this->family[$siblingIndex]['name'];
                    }
                }

            break;

            default:
            return "PERSON_NOT_FOUND\n";
            break;
        }
        if (false == empty($names)) {
            return implode(' ', $names)."\n";
        }

        return "NONE\n";
    }

    /**
     * Common Function to add member to Family.
     *
     * @param string $name   Name of member
     * @param string $gender Gender of member
     */
    protected function addMember(string $name, string $gender)
    {
        $index = array_search($name, array_column($this->family, 'name'));
        if (false === $index) {
            $this->family[] = [
                'id' => count($this->family) + 1,
                'name' => $name,
                'gender' => $gender,
                'spouse' => null,
                'children' => [],
            ];

            $this->updateStore();

            return true;
        }

        return false;
    }

    /**
     * Find parent index in store for the person.
     *
     * @param string $name name of the person
     *
     * @return int|null Index of person in store
     */
    private function findParentIndex(string $name)
    {
        $index = array_search($name, array_column($this->family, 'name'));
        $child = $this->family[$index];
        if (false !== $index) {
            foreach ($this->family as $memberIndex => $member) {
                if (in_array($child['id'], $member['children'])) {
                    return $memberIndex;
                }
            }
        }

        return null;
    }

    /**
     * Add person command.
     *
     * @param string $name   Name of person
     * @param string $gender Gender of person
     */
    private function addPerson(string $name, string $gender)
    {
        // Check for gender
        if (false == in_array($gender, ['Male', 'Female'])) {
            return "PERSON_ADDITION_FAILED - INVALID_GENDER\n";
        } elseif ($this->addMember($name, $gender)) {
            return "PERSON_ADDITION_SUCCEEDED\n";
        }

        return "PERSON_ADDITION_FAILED\n";
    }

    /**
     * Add child in family.
     *
     * @param string $parent Name of parent for which child is being added
     * @param string $name   Name of person
     * @param string $gender Type of child (Male|Female)
     *
     * @return array Names of people with that relationship to person
     */
    private function addChild(string $parent, string $name, string $gender)
    {
        $id = count($this->family) + 1;
        $index = array_search($parent, array_column($this->family, 'name'));

        // Check if parent exists
        if (false === $index) {
            return "PERSON_NOT_FOUND\n";
        }

        // Don't allow addition if parent is Male
        if ('Male' == $this->family[$index]['gender'] && empty($this->family[$index]['spouse'])) {
            return "CHILD_ADDITION_FAILED\n";
        }
        if ($this->addMember($name, $gender)) {
            $this->family[$index]['children'] = array_unique(array_merge($this->family[$index]['children'], [$id]));
            // Add spouse children
            $spouseIndex = array_search($this->family[$index]['spouse'], array_column($this->family, 'id'));
            $this->family[$spouseIndex]['children'] = array_unique(array_merge($this->family[$index]['children'], [$id]));

            return "CHILD_ADDITION_SUCCEEDED\n";
        }

        return "CHILD_ADDITION_FAILED\n";
    }

    /**
     * [addSpouse description].
     *
     * @param string $person [description]
     * @param string $name   [description]
     * @param string $gender [description]
     */
    private function addSpouse(string $parent, string $name, string $gender)
    {
        $id = count($this->family) + 1;
        $index = array_search($parent, array_column($this->family, 'name'));
        if ($this->addMember($name, $gender)) {
            $this->family[$index]['spouse'] = $id;
            $current_member_index = array_search($name, array_column($this->family, 'name'));
            $this->family[$current_member_index]['spouse'] = $this->family[$index]['id'];

            return "SPOUSE_ADDITION_SUCCEEDED\n";
        }

        return "SPOUSE_ADDITION_FAILED\n";
    }

    /**
     * Execute query for family.
     *
     * @param string $input Input required to excute actions on family
     *
     * @return string Output for executions
     */
    public function executeQuery(string $input)
    {
        $query = explode(' ', $input);
        if ('ADD_PERSON' == $query[0]) {
            echo $this->addPerson($query[1], $query[2]);
        } elseif ('ADD_SPOUSE' == $query[0]) {
            echo $this->addSpouse($query[1], $query[2], $query[3]);
        } elseif ('ADD_CHILD' == $query[0]) {
            echo $this->addChild($query[1], $query[2], $query[3]);
        } elseif ('GET_RELATIONSHIP' == $query[0]) {
            echo $this->findRelationship($query[1], $query[2]);
        } else {
            echo "INVALID QUERY: $quey\n";
        }
    }
}

$family = new Family();
$inputs = explode("\n", file_get_contents($argv[1]));
foreach ($inputs as $input) {
    if (true == empty($input)) {
        continue;
    }
    // echo "\n$input\n";
    $family->executeQuery($input);
}

// DEBUG
function dd($var)
{
    var_dump($var);
    exit();
}
