<?php

namespace Yard\BAG\Tests\Config;

use Yard\BAG\Foundation\Config;
use Yard\BAG\Tests\TestCase;

class RepositoryTest extends TestCase
{
    public function setUp(): void
    {
        \WP_Mock::setUp();
    }

    public function tearDown(): void
    {
        \WP_Mock::tearDown();
    }

    /** @test */
    public function gets_value_correctly(): void
    {
        $repository = new Config(__DIR__ . '/config');
        $repository->boot();

        $config = [
            'test'      => [
                'single_file' => true,
            ],
            'directory' => [
                'testfile' => [
                    'in_directory' => 'directory',
                ],
                'multi'    => [
                    'deep' => [
                        'multi_level' => 'works',
                    ],
                ],
            ],
        ];

        $this->assertEquals($config, $repository->all());
        $this->assertEquals(true, $repository->get('test.single_file'));
        $this->assertEquals('directory', $repository->get('directory.testfile.in_directory'));
        $this->assertEquals('works', $repository->get('directory.multi.deep.multi_level'));
    }

    /** @test */
    public function check_setting_of_path(): void
    {
        $repository = new Config(__DIR__ . '/config');

        $path = '/test/path/config/';
        $repository->setPath($path);

        $this->assertEquals($repository->getPath(), $path);
    }

    /** @test */
    public function check_setting_of_protected_nodes(): void
    {
        $repository = new Config(__DIR__ . '/config');
        $repository->boot();

        $expectedConfig = [
            'test'      => [
                'test',
            ],
            'directory' => [
                'testfile' => [
                    'in_directory' => 'directory',
                ],
                'multi'    => [
                    'deep' => [
                        'multi_level' => 'works',
                    ],
                ],
            ],
        ];
        $repository->set('test', ['test']);
        $this->assertEquals($expectedConfig, $repository->all());

        $repository->setProtectedNodes(['test']);
        $repository->set('test', ['test2']);
        $this->assertEquals($expectedConfig, $repository->all());

        $expectedConfig = [
            'test'      => [
                'single_file' => true,
            ],
            'directory' => 'test',
        ];

        $repository = new Config(__DIR__ . '/config');
        $repository->boot();

        $repository->set('directory', 'test');
        $this->assertEquals($expectedConfig, $repository->all());

        $expectedConfig = [
            'test'      => [
                'single_file' => true,
            ],
            'directory' => [
                'test' => 'node',
            ],
        ];
        $repository->set('directory', ['test' => 'node']);
        $this->assertEquals($expectedConfig, $repository->all());

        $expectedConfig = [
            'test'      => [
                'single_file' => true,
            ],
            'directory' => [
                'test' => [
                    'node' => 'nog deeper',
                ],
            ],
        ];
        $repository->set('directory', ['test' => ['node' => 'nog deeper']]);
        $this->assertEquals($expectedConfig, $repository->all());

        $expectedConfig = [
            'test'      => [
                'single_file' => true,
            ],
            'directory' => [
                'testfile' => 'test',
                'multi'    => [
                    'deep' => [
                        'multi_level' => 'works',
                    ],
                ],
            ],
        ];

        $repository = new Config(__DIR__ . '/config');
        $repository->boot();

        $repository->set('directory.testfile', 'test');
        $this->assertEquals($expectedConfig, $repository->all());

        $expectedConfig = [
            'test'      => [
                'single_file' => true,
            ],
            'directory' => [
                'testfile' => [
                    'test' => 'node',
                ],
                'multi'    => [
                    'deep' => [
                        'multi_level' => 'works',
                    ],
                ],
            ],
        ];
        $repository->set('directory.testfile', ['test' => 'node']);
        $this->assertEquals($expectedConfig, $repository->all());

        $expectedConfig = [
            'test'      => [
                'single_file' => true,
            ],
            'directory' => [
                'testfile' => [
                    'test' => [
                        'node' => 'nog deeper',
                    ],
                ],
                'multi'    => [
                    'deep' => [
                        'multi_level' => 'works',
                    ],
                ],
            ],
        ];
        $repository->set('directory.testfile', ['test' => ['node' => 'nog deeper']]);
        $this->assertEquals($expectedConfig, $repository->all());

        $expectedConfig = [
            'test'      => [
                'single_file' => true,
            ],
            'directory' => [
                'testfile' => [
                    'in_directory' => 'directory',
                ],
                'multi'    => 'test',
            ],
        ];

        $repository = new Config(__DIR__ . '/config');
        $repository->boot();

        $repository->set('directory.multi', 'test');
        $this->assertEquals($expectedConfig, $repository->all());

        $expectedConfig = [
            'test'      => [
                'single_file' => true,
            ],
            'directory' => [
                'testfile' => [
                    'in_directory' => 'directory',
                ],
                'multi'    => [
                    'deep' => 'test',
                ],
            ],
        ];

        $repository = new Config(__DIR__ . '/config');
        $repository->boot();

        $repository->set('directory.multi.deep', 'test');
        $this->assertEquals($expectedConfig, $repository->all());

        $expectedConfig = [
            'test'      => [
                'single_file' => true,
            ],
            'directory' => [
                'testfile' => [
                    'in_directory' => 'directory',
                ],
                'multi'    => [
                    'deep' => [
                        'multi_level' => 'works_also_via_set',
                    ],
                ],
            ],
        ];
        $repository->set('directory.multi.deep', ['multi_level' => 'works_also_via_set']);
        $this->assertEquals($expectedConfig, $repository->all());

        $expectedConfig = [
            'test'         => [
                'single_file' => true,
            ],
            'directory'    => [
                'testfile' => [
                    'in_directory' => 'directory',
                ],
                'multi'    => [
                    'deep' => [
                        'multi_level' => 'works',
                    ],
                ],
            ],
            'doesnotexist' => [
                'directory' => [
                    'multi' => [
                        'deep' => null,
                    ],
                ],
            ],
        ];

        $repository = new Config(__DIR__ . '/config');
        $repository->boot();

        $repository->set('doesnotexist.directory.multi.deep');
        $this->assertEquals($expectedConfig, $repository->all());

        $expectedConfig = [
            'test'      => [
                'single_file' => true,
            ],
            'directory' => [
                'testfile' => [
                    'in_directory' => 'directory',
                ],
                'multi'    => [
                    'deep' => [
                        'multi_level' => 'works',
                    ],
                ],
            ],
            ''          => null,
        ];

        $repository = new Config(__DIR__ . '/config');
        $repository->boot();

        $repository->set([null => null]);
        $this->assertEquals($expectedConfig, $repository->all());
    }
}
