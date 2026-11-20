<?php

namespace Kunena\Tests\Unit\User;

use Joomla\Database\DatabaseDriver;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\User\KunenaUser;

class IpBanCategory extends KunenaCategory
{
    public function __construct() {}
    protected function authoriseRead(KunenaUser $user) { return null; }
    protected function authoriseGuestWrite(KunenaUser $user) { return null; }
    protected function authoriseNotBanned(KunenaUser $user) { return null; }
    protected function authoriseNotSection(KunenaUser $user) { return null; }
    protected function authoriseUnlocked(KunenaUser $user) { return null; }
    protected function authoriseChannel(KunenaUser $user) { return null; }
}

class IpBanDatabase extends DatabaseDriver
{
    public array $rows = [];
    private $query;

    public function getNullDate() { return '0000-00-00 00:00:00'; }
    public function quote($value) { return "'" . str_replace("'", "''", $value) . "'"; }
    public function quoteName($value) { return '`' . $value . '`'; }
    public function createQuery() { return new IpBanQuery(); }
    public function setQuery($query) { $this->query = $query; }

    public function loadAssoc()
    {
        preg_match("/`ip` = '([^']*)'/", $this->query->where, $matches);
        $rows = array_reverse($this->rows);

        foreach ($rows as $row) {
            if ($row['ip'] !== ($matches[1] ?? null)) {
                continue;
            }

            if (str_contains($this->query->where, 'expiration >') && $row['expiration'] <= gmdate('Y-m-d H:i:s')) {
                continue;
            }

            return $row;
        }

        return null;
    }
}

class IpBanQuery
{
    public string $where = '';
    public function select($value) { return $this; }
    public function from($value) { return $this; }
    public function where($value) { $this->where = $value; return $this; }
    public function order($value) { return $this; }
    public function setLimit($value) { return $this; }
}
