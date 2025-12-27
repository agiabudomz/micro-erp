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
    
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $pid, $uid);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();

    if ($p && $p['stock'] >= $qty) {
        $total_sale = $p['sell_price'] * $qty;
        $total_profit = ($p['sell_price'] - $p['buy_price']) * $qty;
        
        $ins = $conn->prepare("INSERT INTO sales (user_id, product_id, product_name, quantity, sale_price, total_profit) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->bind_param("iisidd", $uid, $pid, $p['name'], $qty, $p['sell_price'], $total_profit);
        
        if ($ins->execute()) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Registrar Venda | MicroERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 pb-12">

    <div class="max-w-xl mx-auto p-4 sm:p-6">
        
        <div class="flex items-center justify-between mb-8 mt-2">
            <a href="dashboard.php" class="w-11 h-11 flex items-center justify-center rounded-[8px] bg-white text-slate-400 hover:text-blue-600 transition-all shadow-sm border border-slate-200">
                <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
            </a>
            <h2 class="text-lg font-bold text-slate-900 tracking-tight">Registrar Venda</h2>
            <div class="w-11"></div> 
        </div>

        <?php if($msg): ?>
            <div class="<?= $status == 'success' ? 'bg-emerald-500' : 'bg-red-500' ?> text-white p-4 rounded-[8px] mb-6 shadow-lg shadow-indigo-200/20 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
                <ion-icon name="<?= $status == 'success' ? 'checkmark-circle' : 'alert-circle' ?>" class="text-2xl"></ion-icon>
                <p class="font-bold text-sm leading-tight"><?= $msg ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-6 sm:p-8 rounded-[8px] border border-slate-200 shadow-xl shadow-slate-200/50 space-y-6">
            
            <div class="relative">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-3 tracking-widest ml-1">Produto</label>
                
                <div class="relative group">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-blue-600 transition-colors">
                        <ion-icon name="search-outline"></ion-icon>
                    </div>
                    
                    <input type="text" 
                           id="product_search" 
                           placeholder="Buscar pelo nome..." 
                           autocomplete="off"
                           required
                           class="w-full pl-12 pr-4 py-4 bg-slate-50 rounded-[8px] border border-slate-200 outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/5 transition-all font-semibold text-slate-700 placeholder-slate-400">
                    
                    <input type="hidden" name="product_id" id="product_id_hidden" required>
                </div>

                <div id="search_results" class="hidden absolute z-50 w-full mt-2 bg-white rounded-[8px] shadow-2xl border border-slate-200 max-h-64 overflow-y-auto no-scrollbar p-1">
                    <?php 
                    $prods = $conn->query("SELECT * FROM products WHERE user_id = $uid AND stock > 0 ORDER BY name ASC");
                    while($pr = $prods->fetch_assoc()): 
                    ?>
                        <div class="product-item p-3 rounded-[8px] mb-1 cursor-pointer hover:bg-blue-50 transition-all flex justify-between items-center border border-transparent hover:border-blue-100" 
                             data-id="<?= $pr['id'] ?>" 
                             data-name="<?= $pr['name'] ?>"
                             data-stock="<?= $pr['stock'] ?>"
                             data-price="<?= number_format($pr['sell_price'], 2, '.', '') ?>">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-slate-100 rounded-[8px] flex items-center justify-center text-slate-500 font-bold text-xs">
                                    <?= strtoupper(substr($pr['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm tracking-tight"><?= $pr['name'] ?></p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Estoque: <?= $pr['stock'] ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-blue-600 font-bold text-sm"><?= number_format($pr['sell_price'], 2, ',', '.') ?> MT</p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="w-full"> 
                <label class="block text-[9px] font-bold text-slate-500 uppercase mb-2.5 tracking-[0.2em] ml-1 opacity-80">
                    Quantidade Vendida
                </label>
                
                <div class="flex bg-slate-200/50 backdrop-blur-sm rounded-[12px] p-1.5 border border-slate-200/80 shadow-inner"> 
                    
                    <button type="button" 
                        onclick="changeQty(-1)" 
                        class="aspect-square w-[50px] flex items-center justify-center bg-white rounded-[10px] text-slate-500 shadow-[0_2px_8px_-2px_rgba(0,0,0,0.08)] border border-white hover:bg-slate-50 hover:text-red-500 active:scale-95 transition-all duration-200 group">
                        <ion-icon name="remove-outline" class="text-lg group-hover:stroke-[2px]"></ion-icon>
                    </button>

                    <div class="col-span-2 flex flex-col items-center justify-center">
                        <input type="number" 
                            name="quantity" 
                            id="quantity_input" 
                            value="1" 
                            min="1" 
                            required 
                            readonly
                            class="w-full bg-transparent border-none outline-none text-center font-black text-2xl tracking-tighter text-slate-900 selection:bg-transparent">
                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter -mt-1">Unidades</span>
                    </div>

                    <button type="button" 
                        onclick="changeQty(1)" 
                        class="aspect-square flex w-[50px] items-center justify-center bg-blue-600 rounded-[10px] text-white shadow-[0_4px_12px_-2px_rgba(37,99,235,0.3)] border border-blue-500 hover:bg-blue-700 active:scale-95 transition-all duration-200 group">
                        <ion-icon name="add-outline" class="text-xl group-hover:scale-110"></ion-icon>
                    </button>
                </div>
            </div>

            <button type="submit" name="sell_product" class="w-full bg-blue-600 text-white py-5 rounded-[8px] font-bold text-lg shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all flex items-center justify-center gap-3 active:scale-[0.98]">
                Confirmar Venda
                <ion-icon name="checkmark-done-outline" class="text-2xl"></ion-icon>
            </button>
        </form>

        <div class="mt-10">
            <div class="flex items-center justify-between mb-5 px-1">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-[0.2em]">Vendas de Hoje</h3>
                <span class="text-[10px] bg-slate-200 text-slate-600 px-2 py-1 rounded-md font-bold"><?= date('d/m/Y') ?></span>
            </div>
            
            <div class="space-y-3">
                <?php 
                $vendas_hoje = $conn->query("SELECT * FROM sales WHERE user_id = $uid AND DATE(sale_date) = CURDATE() ORDER BY sale_date DESC");
                if($vendas_hoje->num_rows > 0):
                    while($v = $vendas_hoje->fetch_assoc()):
                ?>
                <div class="bg-white p-4 rounded-[8px] border border-slate-200 flex justify-between items-center shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-[8px] flex items-center justify-center font-bold text-xs border border-emerald-100">
                            <?= $v['quantity'] ?>x
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm tracking-tight"><?= $v['product_name'] ?></p>
                            <p class="text-[10px] text-slate-400 font-medium uppercase italic">Lucro: <?= number_format($v['total_profit'], 2, ',', '.') ?> MT</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-slate-900 font-bold text-sm"><?= number_format($v['sale_price'] * $v['quantity'], 2, ',', '.') ?> MT</p>
                        <p class="text-[9px] text-slate-400 font-bold mt-1 tracking-tighter"><?= date('H:i', strtotime($v['sale_date'])) ?></p>
                    </div>
                </div>
                <?php 
                    endwhile; 
                else: 
                ?>
                <div class="text-center py-12 bg-white rounded-[8px] border border-dashed border-slate-300 text-slate-400 text-xs font-bold uppercase tracking-widest">
                    <ion-icon name="cart-outline" class="text-3xl mb-2 block mx-auto opacity-20"></ion-icon>
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

                searchInput.value = name;
                hiddenInput.value = id;
                resultsBox.classList.add('hidden');
                
                // Feedback visual de seleção
                searchInput.classList.add('border-blue-500', 'bg-blue-50/30');
            });
        });

        // Controle de Quantidade
        function changeQty(amount) {
            let current = parseInt(qtyInput.value);
            if (current + amount >= 1) {
                qtyInput.value = current + amount;
            }
        }

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.classList.add('hidden');
            }
        });
    </script>

</body>
</html>