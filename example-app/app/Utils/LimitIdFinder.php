<?php
namespace App\Utils;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;

class LimitIdFinder {

    private $dbTable;
    private $perPage;
    private $lastId;
    private $column;
    private $nextIdQueriesCount = 0;
    /** @var ConnectionInterface  */
    private $connection;

    public function __construct(
        string $dbTable,
        int $perPage = 15,
        int $lastId = 0,
        string $column = 'id'
    )
    {
        /** @var Connection $connection */
        $this->connection = DB::connection();
        $this->dbTable = $dbTable;
        $this->perPage = $perPage;
        $this->lastId = $lastId;
        $this->column = $column;
    }

    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return int
     */
    public function findUpperId()
    {
        $assumedNextId = $this->lastId + $this->perPage;

        $foundRows = $this->connection->table($this->dbTable)
            ->where($this->column, '>', $this->lastId)
            ->where($this->column, '<=', $assumedNextId)
            ->count($this->column);

        $allRows = $this->connection->table($this->dbTable)
            ->count($this->column);

        if($foundRows < $this->perPage && $allRows > $this->perPage){

            $deficit = $this->perPage - $foundRows;
            $nextNotNullId = $assumedNextId;

            for($i = 1; $i <= $deficit; $i++){

                $min = (int)$this->connection->table($this->dbTable)
                    ->where($this->column, '>', $nextNotNullId)
                    ->min($this->column);

                $this->nextIdQueriesCount++;

                if($min < 1){
                    return $nextNotNullId;
                }

                $nextNotNullId = $min;
            }

            return $nextNotNullId;

        }

        return $assumedNextId;
    }

    /**
     * @return int
     */
    public function getNextIdQueriesCount()
    {
        return $this->nextIdQueriesCount;
    }
}
