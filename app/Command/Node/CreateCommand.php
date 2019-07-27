<?php

declare(strict_types=1);

namespace App\Command\Node;

use App\Service\NodeService;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;
use Hyperf\Command\Annotation\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @Command
 */
class CreateCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('node:create');
    }

    public function configure()
    {
        $this->setDescription('Hyperf Nodes Create Command');
    }

    public function handle()
    {
        $service = new NodeService();

        $list = $service->getList();

        $tree = $service->toTree($list);

        $multi_tree = arr2tree($tree, 'node', 'pnode', 'sub');

        file_put_contents(RUNTIME_PATH . 'nodes.php', "<?php \n return " . var_export($multi_tree, true) . ";");

        $this->comment('Nodes Data has successfully created!');
    }

}

