<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$msg = "";
$status = "";

// LÓGICA DE REGISTRO DE VENDA
if (isset($_POST['sell_product'])) {
    $pid = intval($_POST['product_id']);
    $qty = intval($_POST['quantity']);
    
    // Busca os dados do produto para cálculo de lucro
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $pid, $uid);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();

    if ($p && $p['stock'] >= $qty) {
        $total_sale = $p['sell_price'] * $qty;
        $total_profit = ($p['sell_price'] - $p['buy_price']) * $qty;
        
        // 1. Insere na tabela de vendas
        $ins = $conn->prepare("INSERT INTO sales (user_id, product_id, product_name, quantity, sale_price, total_profit) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->bind_param("iisidd", $uid, $pid, $p['name'], $qty, $p['sell_price'], $total_profit);
        
        if ($ins->execute()) {
            // 2. Atualiza o estoque
            $conn->query("UPDATE products SET stock = stock - $qty WHERE id = $pid");
            $msg = "Venda realizada: $qty x {$p['name']}";
            $status = "success";
        }
    } else {
        $msg = "Erro: Estoque insuficiente!";
        $status = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Venda | MicroERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .custom-shadow { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02); }
    </style>
</head>
<body class="pb-10">

    <div class="max-w-xl mx-auto p-4 md:p-8">
        
        <div class="flex items-center justify-between mb-8">
            <a href="dashboard.php" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white text-slate-400 hover:text-indigo-600 transition-all shadow-sm border border-slate-100">
                <ion-icon name="arrow-back-outline" class="text-2xl"></ion-icon>
            </a>
            <h2 class="text-xl font-bold text-slate-800">Registrar Venda</h2>
            <div class="w-12"></div> </div>

        <?php if($msg): ?>
            <div class="<?= $status == 'success' ? 'bg-emerald-500' : 'bg-red-500' ?> text-white p-5 rounded-[2rem] mb-6 shadow-lg flex items-center gap-3 animate-in fade-in zoom-in duration-300">
                <ion-icon name="<?= $status == 'success' ? 'checkmark-circle' : 'alert-circle' ?>" class="text-2xl"></ion-icon>
                <p class="font-bold text-sm"><?= $msg ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-8 rounded-[3rem] border border-slate-100 custom-shadow space-y-8">
            
            <div class="relative">
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-3 ml-2 tracking-[0.2em]">Selecione o Produto</label>
                
                <div class="relative group">
                    <div class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-indigo-600 transition-colors">
                        <ion-icon name="search-outline"></ion-icon>
                    </div>
                    
                    <input type="text" 
                           id="product_search" 
                           placeholder="Buscar pelo nome..." 
                           autocomplete="off"
                           required
                           class="w-full pl-14 pr-6 py-5 bg-slate-50 rounded-[2rem] border-2 border-transparent outline-none focus:border-indigo-100 focus:bg-white focus:ring-4 focus:ring-indigo-500/5 transition-all font-semibold text-slate-700">
                    
                    <input type="hidden" name="product_id" id="product_id_hidden" required>
                </div>

                <div id="search_results" class="hidden absolute z-50 w-full mt-3 bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 max-h-72 overflow-y-auto no-scrollbar p-2">
                    <?php 
                    $prods = $conn->query("SELECT * FROM products WHERE user_id = $uid AND stock > 0 ORDER BY name ASC");
                    while($pr = $prods->fetch_assoc()): 
                    ?>
                        <div class="product-item p-4 rounded-[1.5rem] mb-1 cursor-pointer hover:bg-indigo-50 transition-all flex justify-between items-center" 
                             data-id="<?= $pr['id'] ?>" 
                             data-name="<?= $pr['name'] ?>"
                             data-stock="<?= $pr['stock'] ?>"
                             data-price="R$ <?= number_format($pr['sell_price'], 2, ',', '.') ?>">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 font-bold text-xs uppercase">
                                    <?= substr($pr['name'], 0, 1) ?>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 tracking-tight"><?= $pr['name'] ?></p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">Em estoque: <?= $pr['stock'] ?> un</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-indigo-600 font-bold text-sm">R$ <?= number_format($pr['sell_price'], 2, ',', '.') ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="w-full"> <label class="block text-[10px] font-bold text-slate-400 uppercase mb-3 ml-2 tracking-[0.2em]">
                Quantidade Vendida
            </label>
            <div class="flex items-center gap-2 sm:gap-4"> <button type="button" onclick="changeQty(-1)" class="shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-600 text-xl hover:bg-slate-100 transition-colors">
                    <ion-icon name="remove-outline"></ion-icon>
                </button>

                <input type="number" 
                    name="quantity" 
                    id="quantity_input" 
                    value="1" 
                    min="1" 
                    required 
                    class="flex-1 min-w-0 w-full py-4 bg-slate-50 rounded-2xl border-none outline-none focus:ring-4 focus:ring-indigo-500/5 text-center font-bold text-xl text-slate-800">

                <button type="button" onclick="changeQty(1)" class="shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-600 text-xl hover:bg-slate-100 transition-colors">
                    <ion-icon name="add-outline"></ion-icon>
                </button>
                
            </div>
        </div>

            <button type="submit" name="sell_product" class="w-full bg-indigo-600 text-white py-5 rounded-[2rem] font-bold text-lg shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center justify-center gap-3 group">
                Confirmar Venda
                <ion-icon name="chevron-forward-outline" class="group-hover:translate-x-1 transition-transform"></ion-icon>
            </button>
        </form>

        <div class="mt-12">
            <div class="flex items-center justify-between mb-4 px-2">
                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-widest">Vendas de Hoje</h3>
                <span class="text-[10px] text-slate-400 font-bold"><?= date('d/m/Y') ?></span>
            </div>
            
            <div class="space-y-4">
                <?php 
                $vendas_hoje = $conn->query("SELECT * FROM sales WHERE user_id = $uid AND DATE(sale_date) = CURDATE() ORDER BY sale_date DESC");
                if($vendas_hoje->num_rows > 0):
                    while($v = $vendas_hoje->fetch_assoc()):
                ?>
                <div class="bg-white p-5 rounded-[2rem] border border-slate-100 flex justify-between items-center shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center font-bold text-sm">
                            <?= $v['quantity'] ?>x
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm"><?= $v['product_name'] ?></p>
                            <p class="text-[10px] text-slate-400 font-medium uppercase italic">Lucro: R$ <?= number_format($v['total_profit'], 2, ',', '.') ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-slate-800 font-bold text-sm">R$ <?= number_format($v['sale_price'] * $v['quantity'], 2, ',', '.') ?></p>
                        <p class="text-[9px] text-slate-300 font-bold"><?= date('H:i', strtotime($v['sale_date'])) ?></p>
                    </div>
                </div>
                <?php 
                    endwhile; 
                else: 
                ?>
                <div class="text-center py-10 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200 text-slate-400 text-xs font-medium uppercase tracking-widest">
                    Nenhuma venda hoje
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('product_search');
        const resultsBox = document.getElementById('search_results');
        const hiddenInput = document.getElementById('product_id_hidden');
        const productItems = document.querySelectorAll('.product-item');
        const qtyInput = document.getElementById('quantity_input');

        // Busca em Tempo Real
        searchInput.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            let hasResults = false;

            productItems.forEach(item => {
                const name = item.getAttribute('data-name').toLowerCase();
                if (name.includes(val)) {
                    item.style.display = 'flex';
                    hasResults = true;
                } else {
                    item.style.display = 'none';
                }
            });

            resultsBox.classList.toggle('hidden', val.length === 0 || !hasResults);
        });

        // Seleção do Produto
        productItems.forEach(item => {
            item.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const price = this.getAttribute('data-price');

                searchInput.value = name;
                hiddenInput.value = id;
                resultsBox.classList.add('hidden');
                
                // Feedback visual
                searchInput.classList.remove('focus:border-indigo-100');
                searchInput.classList.add('border-emerald-400');
            });
        });

        // Controle de Quantidade
        function changeQty(amount) {
            let current = parseInt(qtyInput.value);
            if (current + amount >= 1) {
                qtyInput.value = current + amount;
            }
        }

        // Fechar ao clicar fora
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.classList.add('hidden');
            }
        });
    </script>

</body>
</html>