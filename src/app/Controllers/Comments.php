<?php

namespace App\Controllers;

use App\Models\CommentModel;
use CodeIgniter\HTTP\ResponseInterface;
use DateTimeImmutable;

class Comments extends BaseController
{
    private const PER_PAGE = 3;

    public function index(): string
    {
        return view('comments', ['csrfName' => csrf_token(), 'csrfHash' => csrf_hash()]);
    }

    public function list(): ResponseInterface
    {
        $sort = $this->request->getGet('sort') === 'date' ? 'created_at' : 'id';
        $direction = $this->request->getGet('direction') === 'asc' ? 'ASC' : 'DESC';
        $requested = filter_var($this->request->getGet('page'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;

        $model = new CommentModel();
        $total = $model->countAllResults();
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($requested, $pages);
        $model->orderBy($sort, $direction);
        if ($sort !== 'id') {
            $model->orderBy('id', $direction);
        }
        $comments = $model->findAll(self::PER_PAGE, ($page - 1) * self::PER_PAGE);

        return $this->response->setJSON([
            'comments' => $comments,
            'page' => $page,
            'pages' => $pages,
            'total' => $total,
        ]);
    }

    public function create(): ResponseInterface
    {
        $name = trim((string) $this->request->getPost('name'));
        $text = trim((string) $this->request->getPost('text'));
        $date = trim((string) $this->request->getPost('date'));
        $errors = [];

        if (! filter_var($name, FILTER_VALIDATE_EMAIL) || strlen($name) > 254) {
            $errors['name'] = 'Укажите корректный адрес электронной почты.';
        }
        if ($text === '' || mb_strlen($text) > 2000) {
            $errors['text'] = 'Введите комментарий длиной до 2000 символов.';
        }
        $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (! $parsedDate || $parsedDate->format('Y-m-d') !== $date) {
            $errors['date'] = 'Выберите корректную дату.';
        }
        if ($errors !== []) {
            return $this->response->setStatusCode(422)->setJSON(['errors' => $errors, 'csrfHash' => csrf_hash()]);
        }

        $model = new CommentModel();
        $id = $model->insert([
            'name' => $name,
            'text' => $text,
            'date' => $date,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setStatusCode(201)->setJSON(['id' => $id, 'csrfHash' => csrf_hash()]);
    }

    public function delete(int $id): ResponseInterface
    {
        $model = new CommentModel();
        if (! $model->find($id)) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Комментарий уже удалён.', 'csrfHash' => csrf_hash()]);
        }

        $model->delete($id);

        return $this->response->setJSON(['csrfHash' => csrf_hash()]);
    }
}
