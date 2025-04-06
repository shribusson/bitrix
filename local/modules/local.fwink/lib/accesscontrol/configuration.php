<?

namespace Local\Fwink\AccessControl;

class Configuration
{
    public const ACTION_CHECK = 'C';
    public const ACTION_DELETE = 'D';
    public const NAMESPACE_FOR_ENTITIES = "\Local\Fwink\Tables";
    private static $instance;
    private $data = [
        'entitiesDataClasses' => [
            'staff' => self::NAMESPACE_FOR_ENTITIES . '\StaffTable',
			'staffremote' => self::NAMESPACE_FOR_ENTITIES . '\StaffremoteTable',
			'blocks' => self::NAMESPACE_FOR_ENTITIES . '\BlocksTable',
            'userrights' => self::NAMESPACE_FOR_ENTITIES . '\UserrightsTable',
            'roles' => self::NAMESPACE_FOR_ENTITIES . '\RolesTable',
			'posts' => self::NAMESPACE_FOR_ENTITIES . '\PostsTable',
        ],
        'entitiesDependent' => [
            'Staff' => [
                'ID' => [
                    'url' => [
                        'Staff' => Operations::OPERATION_READ
                    ]
                ],
                'FULL_NAME' => [
                    'url' => [
                        'Staff' => Operations::OPERATION_READ
                    ]
                ],
            ],
			'Posts' => [
				'ID' => [
					'url' => [
						'Posts' => Operations::OPERATION_READ
					]
				],
				'FULL_NAME' => [
					'url' => [
						'Posts' => Operations::OPERATION_READ
					]
				],
			],

            'Log' => [
                'AUTHOR_ID' => [
                    'url' => [
                        'Staff' => Operations::OPERATION_READ,
                        'Client' => Operations::OPERATION_READ
                    ],
                ],
            ],
        ],
    ];
    private $loaded = false;

    /**
     * @return Configuration
     */
    public static function getInstance(): Configuration
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get($key)
    {
        if (!$this->loaded) {
            $this->load();
        }
        return $this->data[$key] ?? false;
    }

    private function load(): void
    {
        $this->setRelatedEntities();
        $this->loaded = true;
    }

    private function setRelatedEntities(): void
    {
        $this->data['relatedEntities'] = [

        ];
    }
}
