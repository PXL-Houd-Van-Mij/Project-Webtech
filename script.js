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

    // DRAG & DROP + PREVIEW + COMPRESSIE
    const dropZone = document.getElementById("drop-zone");
    const fileInput = document.getElementById("fileInput");
    const preview = document.getElementById("preview");

    if (dropZone) {

        dropZone.addEventListener("click", () => fileInput.click());

        dropZone.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropZone.classList.add("dragover");
        });

        dropZone.addEventListener("dragleave", () => {
            dropZone.classList.remove("dragover");
        });

        dropZone.addEventListener("drop", (e) => {
            e.preventDefault();
            dropZone.classList.remove("dragover");

            const file = e.dataTransfer.files[0];
            handleImage(file);
        });

        fileInput.addEventListener("change", () => {
            handleImage(fileInput.files[0]);
        });

        function handleImage(file) {
            if (!file.type.startsWith("image/")) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.src = e.target.result;

                img.onload = () => {
                    const canvas = document.createElement("canvas");
                    const ctx = canvas.getContext("2d");

                    const maxWidth = 900;
                    const scale = maxWidth / img.width;

                    canvas.width = maxWidth;
                    canvas.height = img.height * scale;

                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                    canvas.toBlob((blob) => {
                        const compressedFile = new File([blob], file.name, { type: "image/jpeg" });

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(compressedFile);
                        fileInput.files = dataTransfer.files;

                        preview.src = URL.createObjectURL(compressedFile);
                        preview.style.display = "block";
                    }, "image/jpeg", 0.7);
                };
            };
            reader.readAsDataURL(file);
        }
    }

});
// einde T
