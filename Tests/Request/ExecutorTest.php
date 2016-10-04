<?php

/*
 * This file is part of the OverblogGraphQLBundle package.
 *
 * (c) Overblog <http://github.com/overblog/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Overblog\GraphQLBundle\Tests\Resolver;

use GraphQL\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Overblog\GraphQLBundle\Request\Executor;

class ExecutorTest extends \PHPUnit_Framework_TestCase
{
    /** @var Executor */
    private $executor;

    private $request = ['query' => 'query debug{ myField }', 'variables' => [], 'operationName' => null];

    public function setUp()
    {
        $this->executor = new Executor();
    }

    public function testDisabledDebugInfo()
    {
        $this->addSchema();
        $this->assertArrayNotHasKey('debug', $this->executor->disabledDebugInfo()->execute($this->request)->extensions);
    }

    public function testEnabledDebugInfo()
    {
        $this->addSchema();
        $result = $this->executor->enabledDebugInfo()->execute($this->request);

        $this->assertArrayHasKey('debug', $result->extensions);
        $this->assertArrayHasKey('executionTime', $result->extensions['debug']);
        $this->assertArrayHasKey('memoryUsage', $result->extensions['debug']);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage At least one schema should be declare.
     */
    public function testGetSchemaNoSchemaFound()
    {
        $this->executor->getSchema('fake');
    }

    private function addSchema()
    {
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'myField' => [
                    'type' => Type::boolean(),
                    'resolve' => function () {
                        return false;
                    },
                ]
            ]
        ]);

        $this->executor->addSchema('global', new Schema(['query' => $queryType]));
    }
}
