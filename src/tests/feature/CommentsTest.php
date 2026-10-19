<?php

use App\Models\CommentModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

final class CommentsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = 'App';
    protected $refresh = true;

    public function testPageShowsListAndForm(): void
    {
        $result = $this->get('/');

        $result->assertOK();
        $this->assertStringContainsString('id="list-title"', $result->getBody());
        $this->assertStringContainsString('id="comment-form"', $result->getBody());
    }

    public function testInvalidEmailIsRejected(): void
    {
        $result = $this->post('/comments', [
            'name' => 'not-an-email', 'text' => 'Тест', 'date' => '2026-10-19',
            csrf_token() => csrf_hash(),
        ]);

        $result->assertStatus(422);
        $this->assertArrayHasKey('name', json_decode($result->getJSON(), true)['errors']);
        $this->assertSame(0, (new CommentModel())->countAllResults());
    }

    public function testValidCommentIsSavedWithChosenDate(): void
    {
        $result = $this->post('/comments', [
            'name' => 'person@example.com',
            'text' => 'Первый комментарий',
            'date' => '2026-10-19',
            csrf_token() => csrf_hash(),
        ]);

        $result->assertStatus(201);
        $saved = (new CommentModel())->find(json_decode($result->getJSON(), true)['id']);
        $this->assertSame('person@example.com', $saved['name']);
        $this->assertSame('Первый комментарий', $saved['text']);
        $this->assertSame('2026-10-19', $saved['date']);
    }

    public function testPaginationSortingAndDeletion(): void
    {
        $model = new CommentModel();
        foreach (range(1, 4) as $number) {
            $model->insert([
                'name' => "person{$number}@example.com",
                'text' => "Комментарий {$number}",
                'date' => '2026-10-19',
                'created_at' => sprintf('2026-10-19 10:00:%02d', $number),
            ]);
        }

        $first = json_decode($this->get('/comments?sort=id&direction=asc&page=1')->getJSON(), true);
        $this->assertSame(4, $first['total']);
        $this->assertSame(2, $first['pages']);
        $this->assertCount(3, $first['comments']);
        $this->assertSame('person1@example.com', $first['comments'][0]['name']);

        $second = json_decode($this->get('/comments?sort=date&direction=desc&page=2')->getJSON(), true);
        $this->assertSame('person1@example.com', $second['comments'][0]['name']);

        $id = $first['comments'][0]['id'];
        $this->post("/comments/{$id}/delete", [csrf_token() => csrf_hash()])->assertOK();
        $this->assertSame(3, (new CommentModel())->countAllResults());
    }
}
