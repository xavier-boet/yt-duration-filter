document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("[id^=submitData]").forEach(button => {
        button.addEventListener("click", function () {
            const modal = button.closest(".modal");
            const input = modal.querySelector("input");
            const responseMessage = modal.querySelector(".alert");
            const form = modal.querySelector("form");

            const dataType = modal.id.includes("Video") ? "videos" : "channels";
            const url = `/api/${dataType}`;
            const inputValue = input.value.trim();

            if (!inputValue) {
                responseMessage.classList.remove("d-none", "alert-success");
                responseMessage.classList.add("alert-danger");
                responseMessage.textContent = "Please enter a URL.";
                return;
            }

            fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ url: inputValue })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        responseMessage.classList.remove("d-none", "alert-success");
                        responseMessage.classList.add("alert-danger");
                        responseMessage.textContent = data.error;
                    } else {
                        responseMessage.classList.remove("d-none", "alert-danger");
                        responseMessage.classList.add("alert-success");
                        responseMessage.textContent = data.message;

                        form.reset();

                        setTimeout(() => {
                            const bootstrapModal = bootstrap.Modal.getInstance(modal);
                            bootstrapModal.hide();
                        }, 1500);
                    }
                })
                .catch(error => {
                    responseMessage.classList.remove("d-none", "alert-success");
                    responseMessage.classList.add("alert-danger");
                    responseMessage.textContent = "An error has occurred.";
                });
        });
    });

    document.querySelectorAll(".modal").forEach(modal => {
        modal.addEventListener("hidden.bs.modal", function () {
            const input = modal.querySelector("input");
            const responseMessage = modal.querySelector(".alert");

            if (input) {
                input.value = "";
            }

            if (responseMessage) {
                responseMessage.classList.add("d-none");
                responseMessage.textContent = "";
            }
        });
    });    
});
