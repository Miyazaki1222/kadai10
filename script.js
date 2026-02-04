document.addEventListener('DOMContentLoaded', function() {
    // 全ての選手リスト（控え + 打順1〜9）を取得
    const lists = document.querySelectorAll('.player-list');

    lists.forEach(list => {
        new Sortable(list, {
            group: 'baseball',
            animation: 150,
            ghostClass: 'sortable-ghost',
            // 打順スロットには1人しか入れられないように制御
            onAdd: function (evt) {
                if (evt.to.id !== 'bench' && evt.to.children.length > 1) {
                    // すでに選手がいる場合は元の場所（ベンチ等）に戻す
                    evt.from.appendChild(evt.item);
                    alert('この打順には既に選手が登録されています');
                }
            }
        });
    });

    // 保存処理
    document.getElementById('saveBtn').onclick = function() {
        const gameId = document.getElementById('game_id').value;
        if (!gameId) return alert('試合IDを入力してください');

        const orders = [];
        const slots = document.querySelectorAll('.order-slot');

        slots.forEach(slot => {
            const player = slot.querySelector('.player-item');
            if (player) {
                // 同じ行にあるセレクトボックスから守備位置を取得
                const pos = slot.closest('tr').querySelector('.pos-select').value;
                orders.push({
                    player_id: player.dataset.id,
                    batting_order: slot.dataset.order,
                    position: pos
                });
            }
        });

        if (orders.length === 0) return alert('選手が配置されていません');

        fetch('save_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ game_id: gameId, orders: orders })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') alert('オーダーを保存しました！');
        });
    };
});



