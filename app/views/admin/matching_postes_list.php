<h2>Postes disponibles pour le Matching</h2>

<!-- Champ de recherche -->
<input type="text" id="searchPost" placeholder="Rechercher un poste..." 
       style="padding:8px; width:40%; margin-bottom:10px;">

<table border="1" cellpadding="8" style="width:100%;" id="postesTable">
    <thead>
        <tr>
            <th>Poste</th>
            <th>Compétences Requises</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($postes as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['nom_poste']) ?></td>
            <td>
                <?php if ($p['nb_competences'] > 0): ?>
                    <?= $p['nb_competences'] ?> compétence(s)
                <?php else: ?>
                    <span style="color:red;">Aucune définie</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="/matching/poste/<?= $p['id_poste'] ?>">
                    <button>Voir Matching</button>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
// recherche simple
document.getElementById('searchPost').addEventListener('keyup', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#postesTable tbody tr').forEach(tr => {
        const txt = tr.textContent.toLowerCase();
        tr.style.display = txt.includes(q) ? '' : 'none';
    });
});
</script>
