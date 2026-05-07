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

  // ============================================================
  //  AJAX Form Submission — Contact Forms
  // ============================================================

  function getFormData(container) {
    var data = {};
    var fields = ['name', 'company', 'email', 'phone', 'service', 'message'];
    fields.forEach(function(f) {
      var el = container.querySelector('#' + f);
      if (el) {
        data[f] = el.value.trim();
      }
    });
    return data;
  }

  function validateForm(data, isJapanese) {
    var errors = [];
    if (!data.name) errors.push(isJapanese ? 'お名前を入力してください。' : '请输入姓名。');
    if (!data.company) errors.push(isJapanese ? '会社名を入力してください。' : '请输入公司名称。');
    if (!data.email) errors.push(isJapanese ? 'メールアドレスを入力してください。' : '请输入邮箱。');
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
      errors.push(isJapanese ? 'メールアドレスの形式が正しくありません。' : '邮箱格式不正确。');
    }
    return errors;
  }

  function showNotification(type, message, container) {
    // Remove any existing notification
    var existing = container.querySelector('.ajax-notification');
    if (existing) existing.remove();

    var notif = document.createElement('div');
    notif.className = 'ajax-notification ' + type;
    notif.innerHTML = message;
    container.insertBefore(notif, container.firstChild);

    // Auto-scroll to notification
    notif.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function submitForm(container, isJapanese) {
    var data = getFormData(container);

    // Get target email from container data attribute
    var toEmail = container.getAttribute('data-email') || 'info@bl-star.cloud';
    data.to_email = toEmail;
    data.is_japanese = isJapanese ? 1 : 0;

    // Validate
    var errors = validateForm(data, isJapanese);

    // Clear previous field errors
    container.querySelectorAll('input, select, textarea').forEach(function(el) {
      el.style.borderColor = '';
    });

    if (errors.length > 0) {
      showNotification('error', errors.join('<br>'), container);

      // Highlight empty required fields
      ['name', 'company', 'email'].forEach(function(f) {
        var el = container.querySelector('#' + f);
        if (el && !el.value.trim()) {
          el.style.borderColor = 'var(--danger)';
        }
      });
      return;
    }

    // Show loading state
    var submitBtn = container.querySelector('.btn-primary');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = isJapanese ? '送信中...' : '提交中...';
    }

    showNotification('loading', isJapanese
      ? '<div class="spinner"></div> 送信中です...'
      : '<div class="spinner"></div> 正在提交...', container);

    // AJAX POST
    fetch('/api/contact.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    })
    .then(function(response) {
      return response.json().then(function(json) {
        return { status: response.status, json: json };
      });
    })
    .then(function(result) {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = isJapanese ? '送信する' : '提交咨询';
      }

      if (result.json.status === 'success') {
        showNotification('success', result.json.message, container);
        // Reset form
        container.querySelectorAll('input, select, textarea').forEach(function(el) {
          if (el.type !== 'submit') el.value = '';
        });
      } else {
        var msg = result.json.message || (isJapanese ? '送信に失敗しました。' : '提交失败。');
        if (result.json.errors) {
          msg = result.json.errors.join('<br>');
        }
        showNotification('error', msg, container);
      }
    })
    .catch(function(err) {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = isJapanese ? '送信する' : '提交咨询';
      }
      showNotification('error', isJapanese
        ? '通信エラーが発生しました。もう一度お試しください。'
        : '网络错误，请重试。', container);
    });
  }

  // === Contact form submission (AJAX) ===
  var contactFormContainer = document.querySelector('#contactForm');
  if (contactFormContainer) {
    var submitBtn = contactFormContainer.querySelector('.btn-primary');
    if (submitBtn) {
      submitBtn.addEventListener('click', function(e) {
        e.preventDefault();

        var isJapanese = document.documentElement.lang === 'ja' ||
          window.location.pathname.indexOf('/ja/') === 0;

        submitForm(contactFormContainer, isJapanese);
      });
    }
  }

  // === Apply Buttons (Careers) - AJAX dialog ===
  document.querySelectorAll('.apply-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();

      var isJapanese = document.documentElement.lang === 'ja' ||
        window.location.pathname.indexOf('/ja/') === 0;

      var card = this.closest('.position-card');
      var positionName = card ? card.querySelector('h3').textContent.trim() : (isJapanese ? '未指定職位' : '未指定岗位');

      // Create modal overlay
      var overlay = document.createElement('div');
      overlay.className = 'apply-modal-overlay';

      var modal = document.createElement('div');
      modal.className = 'apply-modal';

      var targetEmail = isJapanese ? 'idc_info@bl-star.co.jp' : 'info@bl-star.cloud';

      modal.innerHTML = '' +
        '<div class="apply-modal-header">' +
          '<h3>' + (isJapanese ? '応募フォーム' : '应聘表单') + '</h3>' +
          '<p>' + (isJapanese ? positionName : '应聘岗位: ' + positionName) + '</p>' +
          '<button class="apply-modal-close">&times;</button>' +
        '</div>' +
        '<div class="apply-modal-body">' +
          '<div class="form-group">' +
            '<label for="apply-name">' + (isJapanese ? 'お名前 *' : '姓名 *') + '</label>' +
            '<input type="text" id="apply-name" name="name" placeholder="' + (isJapanese ? '山田太郎' : '张三') + '" required>' +
          '</div>' +
          '<div class="form-group">' +
            '<label for="apply-phone">' + (isJapanese ? '電話番号 *' : '电话 *') + '</label>' +
            '<input type="tel" id="apply-phone" name="phone" placeholder="' + (isJapanese ? '090-1234-5678' : '138-0000-0000') + '" required>' +
          '</div>' +
          '<div class="form-group">' +
            '<label for="apply-email">' + (isJapanese ? 'メールアドレス *' : '邮箱 *') + '</label>' +
            '<input type="email" id="apply-email" name="email" placeholder="your@email.com" required>' +
          '</div>' +
          '<div class="form-group">' +
            '<label for="apply-note">' + (isJapanese ? '備考' : '备注') + '</label>' +
            '<textarea id="apply-note" name="message" rows="3" placeholder="' + (isJapanese ? 'ご連絡可能な時間帯など' : '可联系时间等') + '"></textarea>' +
          '</div>' +
          '<input type="hidden" id="apply-position" value="' + positionName.replace(/"/g, '&quot;') + '">' +
          '<button type="submit" class="btn btn-primary apply-modal-submit" style="width:100%;justify-content:center;">' +
            (isJapanese ? '送信する' : '提交应聘') +
          '</button>' +
        '</div>';

      overlay.appendChild(modal);
      document.body.appendChild(overlay);

      // Animate in
      setTimeout(function() { overlay.classList.add('show'); modal.classList.add('show'); }, 10);

      // Close handlers
      function closeModal() {
        overlay.classList.remove('show');
        modal.classList.remove('show');
        setTimeout(function() { overlay.remove(); }, 300);
      }

      modal.querySelector('.apply-modal-close').addEventListener('click', closeModal);
      overlay.addEventListener('click', function(evt) {
        if (evt.target === overlay) closeModal();
      });

      // Submit handler
      modal.querySelector('.apply-modal-submit').addEventListener('click', function() {
        var name = modal.querySelector('#apply-name').value.trim();
        var phone = modal.querySelector('#apply-phone').value.trim();
        var email = modal.querySelector('#apply-email').value.trim();
        var note = modal.querySelector('#apply-note').value.trim();

        var errors = [];
        if (!name) errors.push(isJapanese ? 'お名前を入力してください。' : '请输入姓名。');
        if (!phone) errors.push(isJapanese ? '電話番号を入力してください。' : '请输入电话。');
        if (!email) errors.push(isJapanese ? 'メールアドレスを入力してください。' : '请输入邮箱。');
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          errors.push(isJapanese ? 'メールアドレスの形式が正しくありません。' : '邮箱格式不正确。');
        }

        // Clear previous field errors
        modal.querySelectorAll('input, textarea').forEach(function(el) { el.style.borderColor = ''; });

        if (errors.length > 0) {
          var notif = modal.querySelector('.ajax-notification');
          if (notif) notif.remove();
          var errDiv = document.createElement('div');
          errDiv.className = 'ajax-notification error';
          errDiv.innerHTML = errors.join('<br>');
          modal.querySelector('.apply-modal-body').insertBefore(errDiv, modal.querySelector('.apply-modal-body').firstChild);
          if (!name) modal.querySelector('#apply-name').style.borderColor = 'var(--danger)';
          if (!phone) modal.querySelector('#apply-phone').style.borderColor = 'var(--danger)';
          if (!email) modal.querySelector('#apply-email').style.borderColor = 'var(--danger)';
          return;
        }

        // Show loading
        var submitBtn2 = modal.querySelector('.apply-modal-submit');
        submitBtn2.disabled = true;
        submitBtn2.textContent = isJapanese ? '送信中...' : '提交中...';

        var body = '【' + (isJapanese ? '応募職位' : '应聘岗位') + '】' + positionName + '\n';
        body += '【' + (isJapanese ? '氏名' : '姓名') + '】' + name + '\n';
        body += '【' + (isJapanese ? '電話' : '电话') + '】' + phone + '\n';
        body += '【' + (isJapanese ? 'メール' : '邮箱') + '】' + email + '\n';
        if (note) {
          body += '【' + (isJapanese ? '備考' : '备注') + '】' + note + '\n';
        }
        body += '\n━━━━━━━━━━━━━━━━━━\n';
        body += (isJapanese ? '応募者より自動送信' : '应聘者自动发送') + '\n';
        body += 'ブルースター株式会社\n';

        fetch('/api/contact.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            name: name,
            company: isJapanese ? '応募者' : '应聘者',
            email: email,
            phone: phone,
            service: 'other',
            message: body,
            to_email: targetEmail,
            is_japanese: isJapanese ? 1 : 0
          })
        })
        .then(function(response) { return response.json(); })
        .then(function(result) {
          submitBtn2.disabled = false;
          submitBtn2.innerHTML = isJapanese ? '送信する' : '提交应聘';

          var notif = modal.querySelector('.ajax-notification');
          if (notif) notif.remove();

          if (result.status === 'success') {
            var successDiv = document.createElement('div');
            successDiv.className = 'ajax-notification success';
            successDiv.innerHTML = isJapanese
              ? 'ご応募ありがとうございます。担当者よりご連絡いたします。'
              : '感谢您的应聘，我们会尽快与您联系。';
            modal.querySelector('.apply-modal-body').insertBefore(successDiv, modal.querySelector('.apply-modal-body').firstChild);

            // Reset form
            modal.querySelector('#apply-name').value = '';
            modal.querySelector('#apply-phone').value = '';
            modal.querySelector('#apply-email').value = '';
            modal.querySelector('#apply-note').value = '';

            // Close modal after 3 seconds
            setTimeout(closeModal, 3000);
          } else {
            var errDiv = document.createElement('div');
            errDiv.className = 'ajax-notification error';
            errDiv.innerHTML = result.message || (isJapanese ? '送信に失敗しました。' : '提交失败。');
            modal.querySelector('.apply-modal-body').insertBefore(errDiv, modal.querySelector('.apply-modal-body').firstChild);
          }
        })
        .catch(function(err) {
          submitBtn2.disabled = false;
          submitBtn2.innerHTML = isJapanese ? '送信する' : '提交应聘';
          var notif = modal.querySelector('.ajax-notification');
          if (notif) notif.remove();
          var errDiv = document.createElement('div');
          errDiv.className = 'ajax-notification error';
          errDiv.innerHTML = isJapanese
            ? '通信エラーが発生しました。もう一度お試しください。'
            : '网络错误，请重试。';
          modal.querySelector('.apply-modal-body').insertBefore(errDiv, modal.querySelector('.apply-modal-body').firstChild);
        });
      });
    });
  });
});
