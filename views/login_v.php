
    <section class="h-100 py-2">
        <div class="container h-100">
            <div class="row justify-content-sm-center h-100">
                <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-7 col-sm-9">
                    <div class="card shadow-lg">
                        <div class="card-body p-5">
                            <h1 class="fs-5 card-title fw-bold mb-4 border-bottom">Login</h1>
                            <?php echo form_open('', 'class="needs-validation" novalidate" id="login_form"'); ?>
                                <div class="mb-3 py-2">
                                    <label class="mb-2 text-muted" for="email">E-Mail Address</label>
                                    <input id="usr_id" type="email" class="form-control" name="usr_id" value="" required autofocus>
                                    <div class="invalid-feedback">
                                        Email을 입력해 주세요
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="mb-2 w-100">
                                        <label class="text-muted" for="password">Password</label>
                                    </div>
                                    <input id="password" type="password" class="form-control" name="password" required>
                                    <div class="invalid-feedback">
                                        비밀번호를 입력해 주세요
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="remember_me" id="remember_me" class="form-check-input">
                                        <label for="remember" class="form-check-label">자동로그인</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary active ms-auto">
                                        Login
                                    </button>
                                </div>

                                <div class="d-flex justify-content-evenly my-2">
                                    <a href="join.html">
                                        회원가입
                                    </a>
                                    <a href="forgot.html">
                                        비밀번호찾기
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
