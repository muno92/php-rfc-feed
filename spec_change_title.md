# titleをrfcテーブルではなくactivityに持たせる

## 背景

- 最初に作成された時点からタイトルが変わったRFCがあった

## 変更後の仕様

- titleはactivityに持たせる
- Feedにはそのactivityのtitleで記載する

## 修正方針

1. activityにtitleカラムを追加する
2. activityのtitleに紐づくrfcのtitleを設定する
3. activityのtitleカラムをnullableからnot nullに変更する
4. rfcのtitleカラムを削除する
5. スクレイピング処理でtitleをactivity側に保存する
6. PHPUnitが通るように調整する
    - 実行方法はREADMEのTesting以下に記載
