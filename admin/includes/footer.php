    <footer class="admin-footer">
        <div class="footer-container">
            <!-- 1. Copyright information and platform name -->
             <div class="footer-copyright">
                <p>&copy; <?php echo date('Y'); ?> <strong>Rentora</strong>. Tous droits réservés.</p>
            </div>
            
             <!-- 2. System information and version details  -->
             <div class="footer-version">
                <span><i class="fa-solid fa-code-branch"></i> Version 1.0.0</span>
                <span>• Admin Console</span>
            </div>
        </div> 
      
 


    </footer>


    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
 <script src="https://unpkg.com/leaflet.fullscreen@2.4.0/Control.FullScreen.js"></script>
 <script src="../js/admin.js"></script>

</body>
</html>

<style>
    /* Admin Footer Styles */
.admin-footer {
    background-color: #ffffff; 
    border-top: 1px solid #e2e8f0;
    padding: 20px 0;
    width: 100%;
    margin-top: auto; 
}

.admin-footer .footer-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 30px;
    
    display: flex;
    flex-direction: column;   
    align-items: center;      
    gap: 8px;                 
    text-align: center;
}

.footer-copyright p {
    font-size: 13px;
    color: #475569; 
    margin: 0;
}

.footer-copyright strong {
    color: #1e3a8a; 
    font-weight: 600;
}

.footer-version {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #94a3b8; 
}

.footer-version i {
    color: #64748b; 
}


/* Responsive Admin Footer */
@media (max-width: 768px) {
    .admin-footer {
        padding: 15px 0;
    }

    .admin-footer .footer-container {
        padding: 0 15px; 
        gap: 5px;       
    }

    .footer-copyright p {
        font-size: 12px; 
    }

    .footer-version {
        font-size: 11px; 
    }
}
</style>