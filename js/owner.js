
// code to control message

    // check if message found in  in page
    const alertMsg = document.querySelector('.alert-msg');
    
    if (alertMsg) {
        // delete message after 3 secont
        setTimeout(() => {
            alertMsg.style.transition = 'opacity 0.5s ease';
            alertMsg.style.opacity = '0';
            
  
            setTimeout(() => {
                alertMsg.remove();
            }, 500);
        }, 3000);
    }


    