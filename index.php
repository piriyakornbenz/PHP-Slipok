<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check Slip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="text-center">ระบบตรวจสอบสลิป SlipOK</h4>
                        <hr>

                        <div class="form-group">
                            <div class="text-center">
                                <p class="mb-2">ธนาคาร ไทยพาณิชย์</p>
                                <span class="badge rounded-pill bg-info mb-2" style="font-size: 0.9rem;">เลขที่บัญชี : <span id="backid">123-456789-0</span></span><br>
                                <button class="btn btn-success btn-sm" onclick="copy_backid()"><i class="fa fa-copy"></i> คัดลอก</button>
                            </div>
                        </div>
                        <form id="slipForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label mb-2" for="slip">สลิปการโอน / Slip</label>
                                <input type="file" id="slip" name="slip" class="form-control" placeholder="กรุณาอัปโหลดสลิปการโอน" accept=".png, .jpg, .jpeg">
                            </div>

                            <button id="submit" type="submit" class="btn btn-danger w-100">
                                <span id="btn-text">ตรวจสอบข้อมูล</span>
                                <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const formslip = document.getElementById('slipForm');
        formslip.addEventListener('submit', function(e) {

            e.preventDefault();
            const formData = new FormData(formslip);
            const submitBtn = document.getElementById('submit');
            const spinner = document.getElementById('spinner');
            const btnText = document.getElementById('btn-text');

            spinner.classList.remove('d-none');
            btnText.innerText = '';
            submitBtn.disabled = true;

            axios.post('check.php', formData)
                .then(response => {
                    const data = response.data;

                    if (data.status == "success") {
                        Swal.fire({
                            title: "ตรวจสอบสำเร็จ",
                            text: data.msg,
                            icon: "success",
                            confirmButtonText: 'ตกลง'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "เกิดข้อผิดพลาด",
                            text: data.msg,
                            icon: "error",
                            confirmButtonText: 'ตกลง'
                        });
                        console.log(data);
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: "เกิดข้อผิดพลาด",
                        text: "ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้ กรุณาลองใหม่อีกครั้ง",
                        icon: "error",
                        confirmButtonText: 'ตกลง',
                        timer: 2000,
                        timerProgressBar: true
                    });
                })
                .finally(() => {
                    spinner.classList.add('d-none');
                    btnText.innerText = 'ตรวจสอบข้อมูล';
                    submitBtn.disabled = false;
                });

        });

        function copy_backid() {
            var copyText = document.getElementById("backid");
            var textArea = document.createElement("textarea");
            textArea.value = copyText.textContent;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand("Copy");

            Swal.fire(
                'คัดลอก',
                'เลขบัญชี ' + textArea.value + ' แล้ว!!',
                'success'
            )

            textArea.remove();

        }
    </script>

</body>

</html>