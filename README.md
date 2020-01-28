# Todo_Done

## DEMO
![Todo_Doneのデモ](https://user-images.githubusercontent.com/56374207/73085367-eeb42a00-3f11-11ea-8c92-0df946b89aba.gif)

## 概要
 「Todo_Done」は、自分がしようと思っていることを記入するTodoリストと、実際に行ったことを記入するDoneリストを同時に見比べられます。  
 もしTodoリストに記入した内容と、実際に行ったことが違えば、実際に行ったことをDoneリストに記入することで、予定していたTodoと、実際に行ったDoneをわかりやすく見ることができます。

## 制作理由
 日々の生活で、自分がしようと思っていることではなく、別のことを行ったことが何度かありました。そこで、TodoとDoneを可視化することによって、何をしようとして何ができなかったかが一目でわかるものが作りたいと思いました。  
また、TodoとDoneを同時に見ることができ、比較できるアプリを作りたいと思い、作成しました。

## 機能
* ログイン、ログアウト機能  
* Todo &middot; Done &middot; メモ登録、一覧、編集、削除機能(CRUD)  
* Todo完了のチェック機能  
* CSRF対策

## 使用方法
 Todo_Doneをページ毎に紹介していきます。

### ログイン
 <img src="https://user-images.githubusercontent.com/56374207/72975167-ae6e8200-3e13-11ea-85a4-a9fdbde5aa27.jpg" alt="Todo_Doneのログインページです" width="400px">
 
* 「ログイン」ページでは、メールアドレスとパスワードを入力し、一致すれば、「トップページ」へ移動します。  
 また、ログイン時にバリデーションが働き、エラーがあれば、エラー表示されます。
* &quot;次回から自動的にログイン&quot;をチェックしてログインすると、入力したメールアドレスがクッキーに二週間保存され、メールアドレスのみ最初から表示されます。
* &quot;ゲストユーザーとしてログイン&quot;で、メールアドレスとパスワードの入力をせずに、ログインできます。
* パスワードは、セキュリティ強化のため、ハッシュ化した値をデータベースに保存しています。（ユーザーの新規登録はありません）

### トップページ
  <img src="https://user-images.githubusercontent.com/56374207/72975180-b3cbcc80-3e13-11ea-8cc1-4d82df5f06e0.jpg" alt="Todo_Doneのトップページです" width="400px">
  
* 「トップページ」では、カレンダーの日付をクリックすると、「Todo_Done」ページへ移動します。  
* Todoを記入している日付には、Todoがピンク色の背景で3文字まで表示されます。Todoが何も書かれていない日付には、まだTodoがありません。  
* 画面左上には、ログインした人の名前を表示します。
* 画面上のナビゲーションは、全ページに読み込まれているため、どのページからでも、「トップページ」へ移動またはログアウトできます。
* ログアウトをクリックすると、クッキーとセッションの値を全て削除し、「ログイン」ページへ移動します。  

### Todo_Done(PCサイズ)
 <img src="https://user-images.githubusercontent.com/56374207/72975200-be866180-3e13-11ea-8902-b32c800b50a3.jpg" alt="Todo_DoneのメインページのPCサイズです" width="400px">
 
* 「Todo_Done」ページでは、TodoとDoneを、&quot;Todoを追加&quot;、&quot;Doneを追加&quot;でそれぞれ登録でき、登録した時間の場所に表示します。  
* Todoのチェックボックスをチェックすると、Todoのタイトルに線が入り、外すと線が消えます。  
* Todoのタイトルをクリックすると、「Memoリスト」へ移動します。
 また、Memoリストにメモが記入されていると、そのTodoのタイトルの末尾に&quot;&bull;&bull;&bull;&quot;が表示されます。  
* Todo • Doneともに、青いボタンの&quot;編集&quot;をクリックすると、「編集確認」ページへ移動し、時間とタイトルを変更できます。  
 また、赤いボタンの&quot;削除&quot;をクリックすると、登録した内容を削除できます。

### Todo_Done(モバイル・タブレットサイズ)
 <img src="https://user-images.githubusercontent.com/56374207/72975204-c0502500-3e13-11ea-9f77-9bd5f192c1b4.jpg" alt="Todoリストのメインページのモバイルサイズです" width="210px">&emsp;&nbsp;<img src="https://user-images.githubusercontent.com/56374207/72975212-c3e3ac00-3e13-11ea-9d4a-8a88e5347042.jpg" alt="Doneリストのメインページのモバイルサイズです" width="210px">
 
* モバイルまたはタブレットサイズの「Todo_Done」では、仕組みはPCサイズと同じで、通常はTodoリストが表示され、Doneリストはモーダルウインドウで表示されます。
* Todoリストで緑色のボタンの&quot;Doneリストを表示&quot;をクリックすると、Doneリストがモーダルウインドウで表示されます。 

### Memoリスト
 <img src="https://user-images.githubusercontent.com/56374207/72975189-b7f7ea00-3e13-11ea-8562-bb6852adac32.jpg" alt="Todo_DoneのMemoリストページです" width="400px">
  
* 「Memoリスト」ページでは、&quot;Memoを追加&quot;でメモを登録でき、内容を表示します。  
* 画面上には、メモをするTodoのタイトルが表示されます。
* Todo_Done」と同様に、青いボタンの&quot;編集&quot;をクリックすると、「編集確認」ページへ移動し、メモの内容を変更できます。  
 また、赤いボタンの&quot;削除&quot;をクリックすると、登録したメモを削除できます。

### 編集確認
 <img src="https://user-images.githubusercontent.com/56374207/72975198-bc240780-3e13-11ea-9271-f970068543fc.jpg" alt="Todo_Doneの編集確認ページです" width="400px">
  
* 「編集確認」ページでは、Todo、Done、Memoのそれぞれの編集確認になり、TodoとDoneでは登録した時間とタイトル、Memoではメモの内容を変更できます。

## Todo_Doneへのリンク
 こちらが実際の制作物になります。  
 __[Todo_Done](https://sinyaweb.com/todo_done/)__

## 制作環境

 言語 &middot;&middot;&middot; HTML5、CSS3、JavaScript、PHP7.3.8	
 
---
 ライブラリ &middot;&middot;&middot; jQuery3.4.1(Ajax含む)
 
---
 データベース &middot;&middot;&middot; MySQL5.7.27

---
 OS &middot;&middot;&middot; macOS Catalina  

---
 エディタ &middot;&middot;&middot; visual studio code  

---
 サーバー &middot;&middot;&middot; XSERVER

