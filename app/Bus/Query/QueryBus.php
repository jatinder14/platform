<?php

namespace Ushahidi\App\Bus\Query;

use Ushahidi\App\Bus\Action;
use Ushahidi\App\Bus\Bus;
use Ushahidi\App\Bus\Command\Command;
use Ushahidi\App\Bus\Command\CommandHandler;
use Ushahidi\App\Bus\Handler;

class QueryBus implements Bus
{
    /**
     * @var Array<Query>
     */
    private $queries;

    public function __construct()
    {
        $this->queries = [];
    }

    public function handle(Action $action): object
    {
        $this->assertIsQuery(get_class($action));
        $this->assertQueryRegistered($action);

        $handler = $this->queries[get_class($action)];

        return resolve($handler)($action);
    }

    public function register(string $action, string $handler): void
    {
        $this->assertIsQuery($action);
        $this->assertIsQueryHandler($handler);

        $this->queries[$action] = $handler;
    }

    /**
     * @param string $action
     * @return void
     */
    private function assertIsQuery(string $action): void
    {
        assert(
            is_subclass_of($action, Query::class),
            sprintf(
                'Invalid argument. Expected instance of %s. Got %s',
                Query::class,
                $action
            )
        );
    }

    /**
     * @param string $handler
     * @return void
     */
    private function assertIsQueryHandler(string $handler): void
    {
        assert(
            is_subclass_of($handler, QueryHandler::clasS),
            sprintf(
                'Invalid argument. Expected instance of %s. Got %s',
                QueryHandler::class,
                $handler
            )
        );
    }

    /**
     * @param Action $action
     * @return void
     */
    private function assertQueryRegistered(Action $action): void
    {
        $actionName = get_class($action);
        assert(
            array_key_exists($actionName, $this->queries),
            sprintf('Invalid argument. %s is not registered.', $actionName)
        );
    }
}
