document.addEventListener("DOMContentLoaded", function () {

    /* =====================================================
       FILE UPLOAD
    ===================================================== */
    const uploadBtn = document.getElementById("uploadBtn");
    const fileInput = document.getElementById("fileInput");
    const fileName  = document.getElementById("fileName");

    uploadBtn.addEventListener("click", () => fileInput.click());

    fileInput.addEventListener("change", () => {
        if (fileInput.files[0]) {
            fileName.innerText = fileInput.files[0].name;
        }
    });


    /* =====================================================
       DOB CALENDAR
    ===================================================== */
    const dobInput = document.getElementById("childDob");
    const dobIcon  = document.querySelector(".dob-icon");

    flatpickr(dobInput, {
        dateFormat: "Y-m-d",
        maxDate: "today",
        disableMobile: true
    });

    if (dobIcon) {
        dobIcon.addEventListener("click", () => {
            dobInput._flatpickr.open();
        });
    }


    /* =====================================================
       AGE-WISE QUESTIONS DATA
    ===================================================== */
    const ageQuestions = {
        "0-1": {
            language: [
                "Does your baby respond to sounds or voices?",
                "Does your baby make cooing or babbling sounds?",
                "Does your baby turn their head toward familiar voices?"
            ],
            motor: [
                "Can your baby lift their head during tummy time?",
                "Can your baby roll from tummy to back or back to tummy?",
                "Can your baby sit with or without support?"
            ],
            social: [
                "Does your baby smile at people?",
                "Does your baby make eye contact?",
                "Does your baby enjoy peek-a-boo?"
            ],
            cognitive: [
                "Does your baby track moving objects?",
                "Does your baby recognize familiar faces?",
                "Does your baby explore toys using hands?"
            ]
        },

        "1-2": {
            language: [
                "Can your child say 2–3 words?",
                "Can your child understand simple instructions?",
                "Does your child point to things they want?"
            ],
            motor: [
                "Can your child walk independently?",
                "Can your child stack blocks?",
                "Can your child feed themselves?"
            ],
            social: [
                "Does your child play simple games?",
                "Does your child imitate actions?",
                "Does your child show affection?"
            ],
            cognitive: [
                "Does your child point to body parts?",
                "Does your child search for hidden toys?",
                "Can your child solve simple problems?"
            ]
        },

        "2-3": {
            language: [
                "Can your child use 2–3 word sentences?",
                "Can your child name objects?",
                "Does your child ask for things verbally?"
            ],
            motor: [
                "Can your child run and climb?",
                "Can your child kick a ball?",
                "Can your child turn book pages?"
            ],
            social: [
                "Does your child play alongside others?",
                "Does your child show interest in sharing?",
                "Does your child show preferences?"
            ],
            cognitive: [
                "Can your child sort objects?",
                "Does your child complete simple puzzles?",
                "Can your child follow 2-step instructions?"
            ]
        },

        "3-4": {
            language: [
                "Can your child speak in full sentences?",
                "Can your child answer simple questions?",
                "Can your child describe familiar objects?"
            ],
            motor: [
                "Can your child hop or jump?",
                "Can your child draw simple shapes?",
                "Can your child put on clothes with little help?"
            ],
            social: [
                "Does your child interact in group play?",
                "Does your child show empathy?",
                "Can your child share willingly?"
            ],
            cognitive: [
                "Does your child recognize colors?",
                "Can your child count 1–5?",
                "Does your child understand simple concepts?"
            ]
        },

        "4-5": {
            language: [
                "Can your child tell short stories?",
                "Can your child ask ‘why’ questions?",
                "Can your child speak clearly?"
            ],
            motor: [
                "Can your child hop on one foot?",
                "Can your child use scissors?",
                "Can your child dress independently?"
            ],
            social: [
                "Does your child follow rules in games?",
                "Does your child cooperate with peers?",
                "Can your child express emotions clearly?"
            ],
            cognitive: [
                "Can your child count 1–10?",
                "Does your child recognize letters?",
                "Can your child solve picture puzzles?"
            ]
        },

        "5-6": {
            language: [
                "Can your child speak fluently?",
                "Can your child retell a story?",
                "Can your child identify beginning sounds?"
            ],
            motor: [
                "Can your child ride a tricycle?",
                "Can your child draw a person?",
                "Can your child catch a ball?"
            ],
            social: [
                "Does your child follow group activities?",
                "Does your child show leadership?",
                "Can your child resolve small conflicts?"
            ],
            cognitive: [
                "Does your child recognize numbers?",
                "Can your child understand time concepts?",
                "Can your child complete larger puzzles?"
            ]
        },

        "6-7": {
            language: [
                "Can your child read simple words?",
                "Can your child write their name?",
                "Can your child explain daily routines?"
            ],
            motor: [
                "Can your child hop while balancing?",
                "Can your child tie shoelaces?",
                "Can your child coordinate both hands?"
            ],
            social: [
                "Does your child make friends easily?",
                "Does your child show leadership skills?",
                "Does your child follow school rules?"
            ],
            cognitive: [
                "Can your child solve simple math problems?",
                "Can your child follow multi-step instructions?",
                "Can your child stay focused 10–15 minutes?"
            ]
        }
    };


    /* =====================================================
       AGE GROUP (UI ONLY)
    ===================================================== */
    function getAgeGroup(dob) {
        const birth = new Date(dob);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        let m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;

        if (age <= 1) return "0-1";
        if (age <= 2) return "1-2";
        if (age <= 3) return "2-3";
        if (age <= 4) return "3-4";
        if (age <= 5) return "4-5";
        if (age <= 6) return "5-6";
        return "6-7";
    }


    /* =====================================================
       LOAD QUESTIONS + SET HIDDEN INPUTS
    ===================================================== */
    function setQ(id, text) {
        document.getElementById(id + "_text").innerText = text;
        document.getElementById(id + "_question").value = text;
    }

    function loadQuestions(ageGroup) {
        const q = ageQuestions[ageGroup];
        if (!q) return;

        q.language.forEach((t, i) => setQ(`lang_q${i+1}`, t));
        q.motor.forEach((t, i) => setQ(`motor_q${i+1}`, t));
        q.social.forEach((t, i) => setQ(`social_q${i+1}`, t));
        q.cognitive.forEach((t, i) => setQ(`cog_q${i+1}`, t));
    }


    /* =====================================================
       STEP SYSTEM
    ===================================================== */
    let currentStep = 1;
    const totalSteps = 5;

    const steps = document.querySelectorAll(".form-step");
    const nextBtn = document.querySelector(".next-btn");
    const prevBtn = document.querySelector(".prev-btn");
    const submitBtn = document.querySelector(".submit-btn");
    const progressFill = document.querySelector(".progress-fill");
    const stepCounter = document.querySelector(".step");

    function showStep(step) {
        steps.forEach((s, i) => s.style.display = i + 1 === step ? "block" : "none");
        progressFill.style.width = (step / totalSteps) * 100 + "%";
        stepCounter.innerText = `${step} of ${totalSteps}`;
        prevBtn.style.display = step === 1 ? "none" : "inline-block";
        nextBtn.style.display = step === totalSteps ? "none" : "inline-block";
        submitBtn.style.display = step === totalSteps ? "block" : "none";
    }

    showStep(currentStep);


    /* =====================================================
       VALIDATION
    ===================================================== */
    function validateStep(step) {
        if (step === 1) {
            if (!fileInput.value) return alert("Upload image"), false;
            if (!childName.value.trim()) return alert("Enter child name"), false;
            if (!childDob.value) return alert("Select DOB"), false;
            if (!document.querySelector("input[name='gender']:checked")) return alert("Select gender"), false;
            if (!childCenter.value) return alert("Select center"), false;
        }
        return true;
    }


    /* =====================================================
       NEXT / PREVIOUS / SUBMIT
    ===================================================== */
    nextBtn.addEventListener("click", () => {
        if (!validateStep(currentStep)) return;

        if (currentStep === 1) {
            loadQuestions(getAgeGroup(childDob.value));
        }

        currentStep++;
        showStep(currentStep);
    });

    prevBtn.addEventListener("click", () => {
        currentStep--;
        showStep(currentStep);
    });

    submitBtn.addEventListener("click", () => {
        document.getElementById("childForm").submit();
    });

});
