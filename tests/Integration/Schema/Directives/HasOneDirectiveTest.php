<?php

namespace Tests\Integration\Schema\Directives;

use Tests\DBTestCase;
use Tests\Utils\Models\Post;
use Tests\Utils\Models\Task;

class HasOneDirectiveTest extends DBTestCase
{
    public function testQueryHasOneRelationship(): void
    {
        // Task with no post
        factory(Task::class)->create();
        // Creates a task and assigns it to this post
        $post = factory(Post::class)->create();

        $this->schema = /** @lang GraphQL */ '
        type Post {
            id: Int
        }
        
        type Task {
            post: Post @hasOne
        }
        
        type Query {
            tasks: [Task!]! @all
        }
        ';

        $this->graphQL(/** @lang GraphQL */ '
        {
            tasks {
                post {
                    id
                }
            }
        }
        ')->assertJson([
            'data' => [
                'tasks' => [
                    [
                        'post' => null,
                    ],
                    [
                        'post' => [
                            'id' => $post->id,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
