document.addEventListener("DOMContentLoaded", () => {

    // BUTTON HOVER ANIMATIE
    document.querySelectorAll(".btn").forEach(btn => {
        btn.addEventListener("mouseenter", () => {
            btn.style.transform = "translateY(-4px) scale(1.05)";
            btn.style.boxShadow = "0 8px 0 rgba(0,0,0,0.2)";
        });

        btn.addEventListener("mouseleave", () => {
            btn.style.transform = "translateY(0) scale(1)";
            btn.style.boxShadow = "0 5px 0 rgba(0,0,0,0.15)";
        });

        btn.addEventListener("mousedown", () => {
            btn.style.transform = "translateY(2px) scale(0.95)";
        });

        btn.addEventListener("mouseup", () => {
            btn.style.transform = "translateY(-4px) scale(1.05)";
        });
    });

    // NAVBAR LINK HOVER
    document.querySelectorAll(".nav-links a").forEach(link => {
        link.addEventListener("mouseenter", () => {
            link.style.opacity = "0.8";
            link.style.transform = "translateY(-2px)";
        });

        link.addEventListener("mouseleave", () => {
            link.style.opacity = "1";
            link.style.transform = "translateY(0)";
        });
    });

    // LOGO BOUNCE EFFECT
    const logo = document.querySelector(".logo");
    if (logo) {
        logo.addEventListener("click", () => {
            logo.style.transition = "transform 0.25s cubic-bezier(.34,1.56,.64,1)";
            logo.style.transform = "scale(1.25)";
            setTimeout(() => logo.style.transform = "scale(1)", 250);
        });
    }

    // INSTAGRAM-LIKE TOGGLE
    const likeBtn = document.getElementById("like-btn");
    const likeCount = document.getElementById("like-count");

    if (likeBtn) {
        likeBtn.addEventListener("click", () => {
            const id = likeBtn.dataset.id;

            fetch("like_toggle.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "id=" + id
            })
            .then(res => res.json())
            .then(data => {
                let count = parseInt(likeCount.textContent);

                if (data.status === "liked") {
                    likeBtn.classList.add("active");
                    likeCount.textContent = (count + 1) + " likes";
                } 
                else if (data.status === "unliked") {
                    likeBtn.classList.remove("active");
                    likeCount.textContent = (count - 1) + " likes";
                }
            });
        });
    }

});