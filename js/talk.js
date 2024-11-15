document.addEventListener("DOMContentLoaded", function () {

    // chat-bodyのスクロールを一番下に移動する関数
    function scrollToBottom() {
        const chatBody = document.querySelector('.chat-body-inner');
        if (chatBody) {
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    }

    // ページが読み込まれたら一番下にスクロール
    scrollToBottom();

    // chat-bodyが存在する場合のみMutationObserverを設定
    const chatBody = document.querySelector('.chat-body-inner');
    if (chatBody) {
        const observer = new MutationObserver(scrollToBottom);
        observer.observe(chatBody, { childList: true });
    }

    // 削除ボタンに確認ダイアログを追加（重複登録防止）
    function addDeleteButtonListeners() {
        const deleteButtons = document.querySelectorAll(".delete-button");
        deleteButtons.forEach(button => {
            if (!button.dataset.listenerAdded) {
                button.addEventListener("click", function (event) {
                    const confirmed = confirm("本当に削除しますか？");
                    if (!confirmed) {
                        event.preventDefault();
                    }
                });
                button.dataset.listenerAdded = "true"; // リスナー登録済みを記録
            }
        });
    }

    // 初期ロード時にリスナーを追加
    addDeleteButtonListeners();

    // chat-body内で要素が追加された場合もリスナーを追加
    if (chatBody) {
        const observer = new MutationObserver(addDeleteButtonListeners);
        observer.observe(chatBody, { childList: true, subtree: true });
    }

    // Enterキーでの送信防止（Shift+Enterで改行）
    const textarea = document.querySelector("textarea[name='post_content']");
    if (textarea) {
        textarea.addEventListener("keydown", function (event) {
            if (event.key === "Enter" && !event.shiftKey) {
                event.preventDefault();
            } else if (event.key === "Enter" && event.shiftKey) {
                textarea.value += "\n";
            }
        });
    }
});


// お気に入り機能
$(".goodBtn").on("click", function () {
    $(this).toggleClass("active");
});

document.addEventListener('DOMContentLoaded', () => {
    const goodBtn = document.querySelector('.goodBtn');

    if (goodBtn) {
        goodBtn.addEventListener('click', async () => {
            const communityId = goodBtn.getAttribute('data-community-id');
            const userId = goodBtn.getAttribute('data-user-id');

            try {
                const response = await fetch('favorites_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        community_id: communityId,
                        user_id: userId,
                    }),
                });

                const result = await response.json();

                if (result.success) {
                    // ボタンの状態を切り替え
                    goodBtn.classList.toggle('active');
                    // カウントの更新
                    const likeCount = goodBtn.querySelector('.like-count');
                    likeCount.textContent = result.favorite_count;
                } else {
                    console.error('お気に入り処理に失敗しました:', result.message);
                }
            } catch (error) {
                console.error('エラー:', error);
            }
            window.location.reload();
        });
    }
});



// 画像プレビュー機能
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');
    const cancelButton = document.getElementById('cancel-button');

    if (file) {
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result; // プレビュー画像のソースを設定
            preview.style.display = 'block'; // 画像を表示
            cancelButton.style.display = 'block'; // キャンセルボタンを表示
        };

        reader.readAsDataURL(file); // ファイルを読み込む
    }
}

function cancelImage() {
    const preview = document.getElementById('preview');
    const cancelButton = document.getElementById('cancel-button');
    const fileInput = document.getElementById('file-input');

    preview.src = ''; // プレビュー画像をクリア
    preview.style.display = 'none'; // プレビューを非表示
    cancelButton.style.display = 'none'; // キャンセルボタンを非表示
    fileInput.value = ''; // ファイル入力をリセット
}

