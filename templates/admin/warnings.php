<?php require_once '../templates/admin/incs/header.php'; ?>
    
    <main class="container">

        <nav class="mb-4">
            <a href="index.php">← Retour à l'administration</a>
        </nav>

        <section>
            <h1>📋 List of warnings</h1>
            
            <?php if (empty($warnings)): ?>
                <article>
                    <p style="text-align: center; color: #6c757d;">
                        Aucune alerte enregistrée.
                    </p>
                </article>
            <?php else: ?>
                <article class="pico-background-grey-50 no-shadow filter-controls">
                    <label for="search">🔍 Rechercher un étudiant:</label>
                    <input type="text" id="search" placeholder="Nom de l'étudiant..." onkeyup="filterWarnings()">
                </article>

                <div style="overflow-x: auto;">
                    <table class="warning-table mt-2 striped" id="warningsTable">
                        <thead>
                            <tr>
                                <th>👤 Étudiant</th>
                                <th>📅 Date</th>
                                <th>#️⃣ Session ID</th>
                                <th>⚠️ Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($warnings as $warning): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($warning['student_name']) ?></strong>
                                    </td>
                                    <td>
                                        <?= (new DateTime($warning['created_at']))->format('d/m/Y H:i') ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($warning['session_id']) ?>
                                    </td>                                   
                                    <td>
                                        <?php if ($warning['warning_type'] == 'tab_switch'): ?>
                                            🗂️ 
                                        <?php elseif ($warning['warning_type'] == 'devtools'): ?>
                                            🛠️ 
                                        <?php endif; ?>
                                        <strong><?= $warning['warning_type'] ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 2rem; text-align: center;">
                    <button onclick="exportToCSV()" role="button" class="outline">
                        📥 Exporter en CSV
                    </button>
                    <button onclick="window.print()" role="button" class="outline">
                        🖨️ Imprimer
                    </button>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
        function filterWarnings() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const table = document.getElementById('warningsTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const studentName = rows[i].getElementsByTagName('td')[0].textContent.toLowerCase();
                if (studentName.includes(searchTerm)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }

        function exportToCSV() {
            const table = document.getElementById('warningsTable');
            const rows = table.querySelectorAll('tr');
            let csv = [];
            
            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].querySelectorAll('th, td');
                let row = [];

                for (let j = 0; j < cells.length; j++) {
                    let cellText = cells[j].textContent.trim();
                    row.push('"' + cellText.replace(/"/g, '""') + '"');
                }
                csv.push(row.join(','));
            }
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', 'warnings_<?= date("Y-m-d") ?>.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    </script>

<?php require_once '../templates/admin/incs/footer.php'; ?>
