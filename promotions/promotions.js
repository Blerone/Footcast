
document.addEventListener("DOMContentLoaded", () => {

   
    const navButtons = document.querySelectorAll("nav button");

    navButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
           
            navButtons.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");
            console.log("Tab clicked:", btn.dataset.tab);
        });
    });

    
    const claimButtons = document.querySelectorAll(".btn-claim");

    claimButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            const code = btn.getAttribute("data-code");
            alert("Promo Code: " + code);
        });
    });

    const loginBtn = document.getElementById("loginBtn");

    if (loginBtn) {
        loginBtn.addEventListener("click", () => {
            alert("Login form would appear here!");
        });
    }

});
