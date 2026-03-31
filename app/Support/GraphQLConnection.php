<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

class GraphQLConnection
{
    /**
     * @return array{nodes: array<int, mixed>, pageInfo: array{hasNextPage: bool, endCursor: ?string}, totalCount: int}
     */
    public static function fromBuilder(Builder $builder, array $pagination = []): array
    {
        $first = max(1, min((int) ($pagination['first'] ?? 20), 50));
        $offset = self::decodeCursor($pagination['after'] ?? null);
        $total = (clone $builder)->count();
        $nodes = $builder->skip($offset)->take($first)->get()->all();
        $nextOffset = $offset + count($nodes);

        return [
            'nodes' => $nodes,
            'pageInfo' => [
                'hasNextPage' => $nextOffset < $total,
                'endCursor' => count($nodes) > 0 ? self::encodeCursor($nextOffset) : null,
            ],
            'totalCount' => $total,
        ];
    }

    private static function decodeCursor(?string $cursor): int
    {
        if (! filled($cursor)) {
            return 0;
        }

        $decoded = base64_decode($cursor, true);

        return $decoded !== false && is_numeric($decoded)
            ? max(0, (int) $decoded)
            : 0;
    }

    private static function encodeCursor(int $offset): string
    {
        return base64_encode((string) $offset);
    }
}
