<?php

namespace App\Services\CashFlow;

use App\Models\CashFlow\Comment;

class CommentService
{
    public function updateComment(array $requestData): Comment
    {
        $comment = $this->getComment($requestData['commentId']);

        unset($requestData['commentId']);

        if (! $comment) {
            return $this->createComment($requestData);
        }

        $comment->update([
            'text' => $requestData['comment'],
            'period' => $requestData['period'],
            'target_info' => $requestData['object'] . ';' . $requestData['reason'],
            'type_id' => $requestData['typeId'],
        ]);

        return $comment;
    }

    public function getComment(int|null $commentId): Comment | null
    {
        return Comment::find($commentId);
    }

    public function createComment(array $requestData): Comment
    {
        return Comment::create([
            'text' => $requestData['comment'],
            'period' => $requestData['period'],
            'target_info' => $requestData['object'] . ';' . $requestData['reason'],
            'type_id' => $requestData['typeId'],
        ]);
    }
}
