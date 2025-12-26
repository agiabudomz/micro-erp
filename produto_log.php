<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$pid = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Consulta de segurança do produto
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $pid, $uid);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();

if (!$p) die("Produto não encontrado ou acesso negado.");

// Histórico de vendas
$logs = $conn->query("SELECT * FROM sales WHERE product_id = $pid ORDER BY sale_date DESC");

// Agregados
$total_v_res = $conn->query("SELECT SUM(quantity) as total_q, SUM(total_profit) as total_p, AVG(sale_price) as avg_p FROM sales WHERE product_id = $pid")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise de Produto | MicroERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .bg-pattern { background-color: #f8fafc; background-image: radial-gradient(#e2e8f0 0.5px, transparent 0.5px); background-size: 11px 11px; }
    </style>
</head>
<body class="bg-pattern min-h-screen p-4 md:p-8">

    <div class="max-w-2xl mx-auto">
        
        <div class="flex items-center justify-between mb-8">
            <a href="dashboard.php" class="flex items-center gap-2 text-slate-500 hover:text-blue-600 font-medium transition-all group">
                <ion-icon name="chevron-back-circle" class="text-2xl group-hover:-translate-x-1 transition-transform"></ion-icon>
                Painel Principal
            </a>
            <div class="flex gap-2">
                <a href="produtos_acao.php?edit=<?= $pid ?>" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition">
                    <ion-icon name="create-outline" class="text-xl"></ion-icon>
                </a>
            </div>
        </div>

        <div class="bg-white p-6 md:p-10 rounded-[2.5rem] border border-slate-200 shadow-xl shadow-slate-200/50 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <ion-icon name="analytics" class="text-9xl"></ion-icon>
            </div>

            <header class="relative z-10">
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-600 text-[10px] font-bold uppercase tracking-tighter rounded-full">Análise Individual</span>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-400 text-[10px] font-medium uppercase tracking-widest">ID #<?= $pid ?></span>
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 mb-8"><?= htmlspecialchars($p['name']) ?></h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-blue-600 p-6 rounded-3xl text-white shadow-lg shadow-blue-200">
                        <div class="flex justify-between items-start mb-4">
                            <p class="text-[10px] opacity-80 uppercase font-bold tracking-widest">Lucro Total Acumulado</p>
                            <ion-icon name="trending-up" class="text-xl"></ion-icon>
                        </div>
                        <p class="text-2xl font-bold"><?= number_format($total_v_res['total_p'] ?? 0, 0, ',', '.') ?> MT</p>
                    </div>

                    <div class="bg-slate-50 border border-slate-100 p-6 rounded-3xl">
                        <div class="flex justify-between items-start mb-4">
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest">Volume Vendido</p>
                            <ion-icon name="cart-outline" class="text-slate-400 text-xl"></ion-icon>
                        </div>
                        <p class="text-2xl font-bold text-slate-800"><?= $total_v_res['total_q'] ?? 0 ?> <span class="text-sm font-medium text-slate-400">unid.</span></p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-4 px-2">
                    <div class="text-center">
                        <p class="text-[9px] text-slate-400 uppercase font-bold">Estoque Atual</p>
                        <p class="text-sm font-bold text-slate-700"><?= $p['stock'] ?></p>
                    </div>
                    <div class="text-center border-x border-slate-100">
                        <p class="text-[9px] text-slate-400 uppercase font-bold">Preço Médio</p>
                        <p class="text-sm font-bold text-slate-700"><?= number_format($total_v_res['avg_p'] ?? 0, 0, ',', '.') ?> MT</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[9px] text-slate-400 uppercase font-bold">Margem</p>
                        <p class="text-sm font-bold text-emerald-500">
                            <?php 
                            if($p['buy_price'] > 0) {
                                $margem = (($p['sell_price'] - $p['buy_price']) / $p['buy_price']) * 100;
                                echo number_format($margem, 0) . '%';
                            } else { echo '-'; }
                            ?>
                        </p>
                    </div>
                </div>
            </header>
        </div>

        <div class="flex items-center gap-4 mb-6 ml-2">
            <h3 class="font-bold text-slate-800 tracking-tight">Linha do Tempo de Vendas</h3>
            <div class="h-[1px] flex-1 bg-slate-200"></div>
        </div>

        <div class="space-y-4">
            <?php while($l = $logs->fetch_assoc()): ?>
            <div class="group bg-white p-5 rounded-3xl border border-slate-200 hover:border-blue-300 hover:shadow-md transition-all flex justify-between items-center relative overflow-hidden">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <ion-icon name="bag-check-outline" class="text-xl"></ion-icon>
                    </div>
                    <div>
                        <p class="text-slate-900 font-bold leading-tight"><?= $l['quantity'] ?> unidades</p>
                        <p class="text-[10px] text-slate-400 flex items-center gap-1 mt-1">
                            <ion-icon name="calendar-clear-outline"></ion-icon>
                            <?= date('d M, Y', strtotime($l['sale_date'])) ?>
                            <span class="mx-1">•</span>
                            <ion-icon name="time-outline"></ion-icon>
                            <?= date('H:i', strtotime($l['sale_date'])) ?>
                        </p>
                    </div>
                </div>

                <div class="text-right">
                    <p class="text-slate-900 font-extrabold text-sm tracking-tight">
                        <?= number_format($l['sale_price'] * $l['quantity'], 0, ',', '.') ?> MT
                    </p>
                    <p class="text-[9px] text-emerald-500 font-bold uppercase tracking-tighter">
                        + <?= number_format($l['total_profit'], 0, ',', '.') ?> MT lucro
                    </p>
                </div>
            </div>
            <?php endwhile; ?>

            <?php if($logs->num_rows == 0): ?>
            <div class="bg-white rounded-[2rem] p-12 text-center border-2 border-dashed border-slate-200">
                <ion-icon name="cloud-offline-outline" class="text-5xl text-slate-200 mb-4"></ion-icon>
                <p class="text-slate-400 font-medium italic">Nenhuma movimentação registrada para este produto.</p>
            </div>
            <?php endif; ?>
        </div>

        <footer class="mt-12 mb-8 text-center text-slate-400 text-[10px] uppercase tracking-[0.2em]">
            © 2025 MicroERP Analytics - Dados atualizados em tempo real
        </footer>
    </div>

</body>
</html>