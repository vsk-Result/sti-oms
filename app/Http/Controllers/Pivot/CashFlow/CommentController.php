<?php

namespace App\Http\Controllers\Pivot\CashFlow;

use App\Http\Controllers\Controller;
use App\Services\CashFlow\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(private CommentService $commentService) {}

    public function update(Request $request): JsonResponse
    {
        $comment = $this->commentService->updateComment($request->all());

        return response()->json(['status' => 'success', 'message' => 'Комментарий сохранен', 'comment_id' => $comment->id]);
    }
}
