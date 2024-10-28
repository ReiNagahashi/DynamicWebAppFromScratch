<?php
namespace Response;
// HTTPRendererはサーバサイドでのレンダリングを行う役割を持つ
// 目的：サーバサイドレンダリング・クライアントサイドレンダリングの両方において、
// 再利用・関心の分離・拡張性(ex:バリデーション機能の追加)を確保する
interface HTTPRenderer{
    // 適切なHTTPレスポンスを設定する役割
    public function getFields(): array;
    // HTTPレスポンスボティのコンテンツを返す
    public function getContent(): string;
}