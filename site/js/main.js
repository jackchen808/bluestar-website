// BLUESTAR Website - JavaScript
document.addEventListener('DOMContentLoaded', function() {
  // === Navbar scroll effect ===
  const navbar = document.querySelector('.navbar');
  window.addEventListener('scroll', function() {
    if (window.scrollY > 20) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });

  // === Mobile nav toggle ===
  const navToggle = document.querySelector('.nav-toggle');
  const navLinks = document.querySelector('.navbar-links');
  if (navToggle) {
    navToggle.addEventListener('click', function() {
      navLinks.classList.toggle('open');
    });
    // Close nav on link click
    document.querySelectorAll('.navbar-links a').forEach(function(link) {
      link.addEventListener('click', function() {
        navLinks.classList.remove('open');
      });
    });
  }

  // === FAQ accordion ===
  document.querySelectorAll('.faq-question').forEach(function(q) {
    q.addEventListener('click', function() {
      const item = this.parentElement;
      item.classList.toggle('open');
    });
  });

  // === Animated counter ===
  const counters = document.querySelectorAll('.counter');
  if (counters.length > 0) {
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          const counter = entry.target;
          const target = parseInt(counter.getAttribute('data-target'), 10);
          const suffix = counter.getAttribute('data-suffix') || '';
          const isFloat = target !== parseInt(counter.getAttribute('data-target'));
          animateCounter(counter, target, suffix, isFloat);
          observer.unobserve(counter);
        }
      });
    }, { threshold: 0.5 });

    counters.forEach(function(c) { observer.observe(c); });
  }

  function animateCounter(el, target, suffix, isFloat) {
    const steps = 60;
    const increment = target / steps;
    let current = 0;
    let step = 0;

    function update() {
      step++;
      if (step >= steps) {
        el.textContent = (isFloat ? target.toFixed(1) : target.toLocaleString()) + suffix;
        return;
      }
      current = Math.min(current + increment, target);
      el.textContent = (isFloat ? current.toFixed(1) : Math.floor(current).toLocaleString()) + suffix;
      requestAnimationFrame(update);
    }
    update();
  }

  // === Mailto Helper Function ===
  window.sendMailto = function(to, subject, body) {
    var mailtoLink = 'mailto:' + to + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);
    window.location.href = mailtoLink;
  };

  // === Form Validation + Mailto ===
  var contactForm = document.querySelector('.contact-form');
  if (contactForm) {
    var submitBtn = contactForm.querySelector('.btn-primary');
    if (submitBtn) {
      submitBtn.addEventListener('click', function(e) {
        e.preventDefault();

        var name = document.getElementById('name');
        var company = document.getElementById('company');
        var email = document.getElementById('email');
        var service = document.getElementById('service');
        var message = document.getElementById('message');
        var phone = document.getElementById('phone');

        // Validate required fields
        var valid = true;
        var firstInvalid = null;
        [name, company, email, service].forEach(function(field) {
          if (!field || !field.value.trim()) {
            field.style.borderColor = 'var(--danger)';
            valid = false;
            if (!firstInvalid) firstInvalid = field;
          } else {
            field.style.borderColor = '';
          }
        });

        // Validate email format
        if (email && email.value.trim()) {
          var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailPattern.test(email.value.trim())) {
            email.style.borderColor = 'var(--danger)';
            valid = false;
            if (!firstInvalid) firstInvalid = email;
          }
        }

        if (!valid) {
          if (firstInvalid) firstInvalid.focus();
          return;
        }

        // Determine language from HTML lang or URL
        var isJapanese = document.documentElement.lang === 'ja' ||
          window.location.pathname.indexOf('/ja/') === 0;

        var subject, body;
        if (isJapanese) {
          subject = 'BLUESTARへのお問い合わせ - ' + company.value.trim() + ' - ' + name.value.trim();
          body = '━━━━━━━━━━━━━━━━━━\n';
          body += '【氏名】' + name.value.trim() + '\n';
          body += '【会社名】' + company.value.trim() + '\n';
          body += '【メール】' + email.value.trim() + '\n';
          body += '【電話】' + (phone ? phone.value.trim() : '') + '\n';
          body += '【種別】' + (service ? service.options[service.selectedIndex].text : '') + '\n';
          body += '━━━━━━━━━━━━━━━━━━\n\n';
          body += '【お問い合わせ内容】\n' + (message ? message.value.trim() : '') + '\n\n';
          body += '━━━━━━━━━━━━━━━━━━\n';
          body += 'ブルースター株式会社\n';
          body += '〒169-0075 東京都新宿区高田馬場1-31-8 高田馬場ダイカンプラザ625号\n';
          body += 'TEL: 03-6824-5796\n';
          body += 'Email: idc_info@bl-star.co.jp';
        } else {
          subject = 'BLUESTAR 咨询服务 - ' + company.value.trim() + ' - ' + name.value.trim();
          body = '━━━━━━━━━━━━━━━━━━\n';
          body += '【姓名】' + name.value.trim() + '\n';
          body += '【公司名称】' + company.value.trim() + '\n';
          body += '【邮箱】' + email.value.trim() + '\n';
          body += '【电话】' + (phone ? phone.value.trim() : '') + '\n';
          body += '【服务需求】' + (service ? service.options[service.selectedIndex].text : '') + '\n';
          body += '━━━━━━━━━━━━━━━━━━\n\n';
          body += '【项目描述】\n' + (message ? message.value.trim() : '') + '\n\n';
          body += '━━━━━━━━━━━━━━━━━━\n';
          body += 'ブルースター株式会社 (BlueStar Co.,Ltd.)\n';
          body += '〒169-0075 東京都新宿区高田馬場1-31-8 高田馬場ダイカンプラザ625号\n';
          body += 'TEL: 03-6824-5796\n';
          body += 'Email: info@bl-star.cloud';
        }

        var recipient = isJapanese ? 'idc_info@bl-star.co.jp' : 'info@bl-star.cloud';

        // Show success message then open mailto
        var successMsg = submitBtn.parentElement.querySelector('.form-success');
        if (!successMsg) {
          successMsg = document.createElement('div');
          successMsg.className = 'form-success';
          successMsg.innerHTML = '<div class="icon">✅</div>' +
            '<h4>' + (isJapanese ? 'メールクライアントを起動します' : '正在打开邮件客户端') + '</h4>' +
            '<p>' + (isJapanese ? 'メール内容をご確認の上、送信してください。' : '请确认邮件内容后发送。') + '</p>';
          submitBtn.parentElement.insertBefore(successMsg, submitBtn.nextSibling);
        }
        successMsg.classList.add('show');

        setTimeout(function() {
          window.sendMailto(recipient, subject, body);
        }, 300);
      });
    }
  }

  // === Apply Buttons (Careers) ===
  document.querySelectorAll('.apply-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();

      var card = this.closest('.position-card');
      var positionName = card ? card.querySelector('h3').textContent.trim() : '未指定職位';

      var isJapanese = document.documentElement.lang === 'ja' ||
        window.location.pathname.indexOf('/ja/') === 0;

      var nameInput = prompt(isJapanese ?
        'ご氏名を入力してください：' :
        '请输入您的姓名：');

      if (!nameInput || !nameInput.trim()) return;

      var subject, body;
      if (isJapanese) {
        subject = '応募: ' + positionName + ' - ' + nameInput.trim();
        body = '【応募職位】' + positionName + '\n';
        body += '【氏名】' + nameInput.trim() + '\n\n';
        body += '━━━━━━━━━━━━━━━━━━\n';
        body += 'ご連絡先（電話番号・メールアドレス）を以下にご記入ください：\n\n';
        body += '【電話番号】\n';
        body += '【メール】\n';
        body += '━━━━━━━━━━━━━━━━━━\n\n';
        body += '※ 履歴書・職務経歴書を添付してご送信ください。\n';
        body += '担当者より追ってご連絡いたします。';
      } else {
        subject = '应聘: ' + positionName + ' - ' + nameInput.trim();
        body = '【应聘岗位】' + positionName + '\n';
        body += '【姓名】' + nameInput.trim() + '\n\n';
        body += '━━━━━━━━━━━━━━━━━━\n';
        body += '请补充以下联系方式：\n\n';
        body += '【电话】\n';
        body += '【邮箱】\n';
        body += '━━━━━━━━━━━━━━━━━━\n\n';
        body += '※ 请将简历和作品集（如有）作为附件发送。\n';
        body += '我们会在收到后尽快与您联系。';
      }

      window.sendMailto(
        isJapanese ? 'idc_info@bl-star.co.jp' : 'info@bl-star.cloud',
        subject,
        body
      );
    });
  });
});
