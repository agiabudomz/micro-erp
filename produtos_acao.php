<?php
require_once 'config/db.php';
session_start();

// Proteção de acesso
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$edit_id = $_GET['edit'] ?? null;
$p = ['name' => '', 'buy_price' => '', 'sell_price' => '', 'stock' => ''];
$message = null; // Variável para a mensagem de feedback

// Lógica de Exclusão
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();
    header("Location: dashboard.php?deleted=1");
    exit;
}

// Lógica de Edição (Busca dados)
if ($edit_id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $edit_id, $uid);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc() ?? $p;
}

// Salvar (Insert ou Update) - SEM REDIRECIONAR NO SUCESSO
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $buy = $_POST['buy_price'];
    $sell = $_POST['sell_price'];
    $stock = $_POST['stock'];

    if ($edit_id) {
        $stmt = $conn->prepare("UPDATE products SET name=?, buy_price=?, sell_price=?, stock=? WHERE id=? AND user_id=?");
        $stmt->bind_param("sddiii", $name, $buy, $sell, $stock, $edit_id, $uid);
        $op = "atualizado";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (user_id, name, buy_price, sell_price, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isddi", $uid, $name, $buy, $sell, $stock);
        $op = "adicionado";
    }
    
    if ($stmt->execute()) {
        $message = ["type" => "success", "text" => "Produto $op com sucesso!"];
        // Se for uma nova inserção, limpamos os campos para o próximo
        if (!$edit_id) {
            $p = ['name' => '', 'buy_price' => '', 'sell_price' => '', 'stock' => ''];
        } else {
            // Se for edição, atualiza os dados da tela com o que foi postado
            $p = ['name' => $name, 'buy_price' => $buy, 'sell_price' => $sell, 'stock' => $stock];
        }
    } else {
        $message = ["type" => "error", "text" => "Erro ao processar produto."];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $edit_id ? 'Editar' : 'Novo' ?> Produto - MicroERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .input-group:focus-within label { color: #2563eb; }
        .input-group:focus-within .icon-box { color: #2563eb; border-color: #2563eb; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 sm:p-6 text-slate-800">

    <div class="w-full max-w-xl">
        <a href="dashboard.php" class="inline-flex items-center gap-2 text-slate-400 hover:text-blue-600 transition mb-6 group">
            <ion-icon name="arrow-back-outline" class="text-xl group-hover:-translate-x-1 transition-transform"></ion-icon>
            <span class="font-medium text-sm">Voltar ao painel</span>
        </a>

        <div class="bg-white rounded-[8px] shadow-2xl shadow-slate-200/60 p-8 sm:p-10 border border-slate-100">
            
            <header class="mb-10 flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-600 text-white rounded-[8px] flex items-center justify-center shadow-lg shadow-blue-200">
                    <ion-icon name="<?= $edit_id ? 'create' : 'add-circle' ?>" class="text-2xl"></ion-icon>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 leading-tight"><?= $edit_id ? 'Editar' : 'Novo' ?> Produto</h2>
                    <p class="text-slate-400 text-sm">Preencha os detalhes do seu estoque.</p>
                </div>
            </header>

            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-[8px] flex items-center gap-3 <?= $message['type'] === 'success' ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100' ?>">
                    <ion-icon name="<?= $message['type'] === 'success' ? 'checkmark-circle' : 'alert-circle' ?>" class="text-xl"></ion-icon>
                    <span class="text-sm font-medium"><?= $message['text'] ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                
                <div class="input-group">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest ml-1 mb-2 block">Identificação</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 icon-box transition-all">
                            <ion-icon name="pricetag-outline" class="text-xl"></ion-icon>
                        </div>
                        <input type="text" name="name" value="<?= htmlspecialchars($p['name']) ?>" required
                            class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-[8px] outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all placeholder-slate-400"
                            placeholder="Ex: Camiseta Algodão Premium">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="input-group">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest ml-1 mb-2 block">Preço de Custo (MT)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 icon-box transition-all">
                                <ion-icon name="trending-down-outline" class="text-xl text-red-400"></ion-icon>
                            </div>
                            <input type="number" step="0.01" name="buy_price" value="<?= $p['buy_price'] ?>" required
                                class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-[8px] outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all"
                                placeholder="0,00">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest ml-1 mb-2 block">Preço de Venda (MT)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 icon-box transition-all">
                                <ion-icon name="trending-up-outline" class="text-xl text-green-500"></ion-icon>
                            </div>
                            <input type="number" step="0.01" name="sell_price" value="<?= $p['sell_price'] ?>" required
                                class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-[8px] outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all"
                                placeholder="0,00">
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest ml-1 mb-2 block">Quantidade em Estoque</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 icon-box transition-all">
                            <ion-icon name="cube-outline" class="text-xl"></ion-icon>
                        </div>
                        <input type="number" name="stock" value="<?= $p['stock'] ?>" required
                            class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-[8px] outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all"
                            placeholder="Ex: 10">
                    </div>
                </div>

                <div class="pt-6 space-y-3">
                    <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-[8px] font-bold shadow-lg shadow-blue-200 flex items-center justify-center gap-2 transform active:scale-[0.98] transition-all">
                        <ion-icon name="save-outline" class="text-xl"></ion-icon>
                        <span><?= $edit_id ? 'Salvar Alterações' : 'Cadastrar Produto' ?></span>
                    </button>
                    
                    <a href="dashboard.php" 
                        class="w-full flex items-center justify-center py-4 text-slate-400 font-medium hover:text-slate-600 transition">
                        Cancelar e voltar
                    </a>
                </div>

            </form>
        </div>

        <p class="mt-8 text-center text-slate-400 text-xs tracking-wide">
            Dica: O lucro é calculado automaticamente no seu painel principal.
        </p>
    </div>

</body>
</html>