<footer class="main-footer">
    <div class="footer-content">
        <span>© <?php echo date("Y"); ?> GHTCA - Gestion des logs</span>
        <span class="footer-separator">|</span>
        <span>Supervision des postes et des connexions</span>
    </div>
</footer>

<style>

/* ----------------------------
   FOOTER
---------------------------- */
.main-footer {
    margin-top: 40px;
    padding: 15px 20px;
    background: #2c3e50;
    color: #ecf0f1;
    text-align: center;
    border-top: 3px solid #3498db;
}

/* CONTENU */
.footer-content {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    flex-wrap: wrap;
}

/* SEPARATEUR */
.footer-separator {
    opacity: 0.6;
}

/* HOVER */
.main-footer span:hover {
    color: #3498db;
    transition: 0.2s;
}

/* RESPONSIVE */
@media (max-width: 600px) {
    .footer-content {
        flex-direction: column;
        gap: 5px;
    }
}

</style>