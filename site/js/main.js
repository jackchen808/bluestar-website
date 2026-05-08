// BLUESTAR Website - JavaScript
document.addEventListener('DOMContentLoaded', function() {

  // === Navigation scroll effect ===
  var navbar = document.querySelector('.navbar');
  window.addEventListener('scroll', function() {
    if (window.scrollY > 10) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });

  // === Mobile nav toggle ===
  var navToggle = document.querySelector('.nav-toggle');
  var navLinks = document.querySelector('.navbar-links');
  if (navToggle) {
    navToggle.addEventListener('click', function() {
      navLinks.classList.toggle('open');
    });
  }

  // === Counter Animation ===
  var counters = document.querySelectorAll('.counter');
  if (counters.length > 0) {
    var counterObserver = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          var el = entry.target;
          var target = parseFloat(el.getAttribute('data-target'));
          var suffix = el.getAttribute('data-suffix') || '';
          var duration = 2000;
          var start = performance.now();

          function animate(now) {
            var elapsed = now - start;
            var progress = Math.min(elapsed / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = target * eased;
            if (target % 1 === 0) {
              el.textContent = Math.floor(current).toLocaleString() + suffix;
            } else {
              el.textContent = current.toFixed(2) + suffix;
            }
            if (progress < 1) {
              requestAnimationFrame(animate);
            }
          }
          requestAnimationFrame(animate);
          counterObserver.unobserve(el);
        }
      });
    }, { threshold: 0.3 });
    counters.forEach(function(c) { counterObserver.observe(c); });
  }

  // === FAQ Accordion ===
  var faqItems = document.querySelectorAll('.faq-item');
  faqItems.forEach(function(item) {
    var question = item.querySelector('.faq-question');
    if (question) {
      question.addEventListener('click', function() {
        item.classList.toggle('open');
      });
    }
  });

  // === Notification helper ===
  function showNotification(message, type) {
    var notif = document.getElementById('notification');
    if (!notif) {
      notif = document.createElement('div');
      notif.id = 'notification';
      notif.className = 'ajax-notification';
      document.body.appendChild(notif);
    }
    notif.className = 'ajax-notification ' + type;
    notif.textContent = message;
    notif.style.display = 'block';
    notif.style.opacity = '1';
    setTimeout(function() {
      notif.style.opacity = '0';
      setTimeout(function() { notif.style.display = 'none'; }, 500);
    }, 4000);
  }

  // === Contact Form: AJAX submit ===
  var contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
      e.preventDefault();
      var submitBtn = contactForm.querySelector('.btn-primary');
      var origText = submitBtn.innerHTML;
      submitBtn.innerHTML = '发送中...';
      submitBtn.disabled = true;

      var data = {
        name: document.getElementById('name').value,
        company: document.getElementById('company').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        service: document.getElementById('service').value,
        message: document.getElementById('message').value,
        type: 'inquiry',
        to_email: contactForm.getAttribute('data-email') || 'info@bl-star.cloud'
      };

      var apiUrl = window.location.hostname.indexOf('github.io') === -1 ? 'api/contact.php' : 'https://bl-star.co.jp/api/contact.php';

      fetch(apiUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      })
      .then(function(r) { return r.json(); })
      .then(function(result) {
        if (result.status === 'success') {
          showNotification('✓ ' + result.message, 'success');
          contactForm.reset();
        } else {
          showNotification('✗ ' + result.message, 'error');
        }
      })
      .catch(function() {
        showNotification('✗ 发送失败，请直接发送邮件至 info@bl-star.cloud', 'error');
      })
      .finally(function() {
        submitBtn.innerHTML = origText;
        submitBtn.disabled = false;
      });
    });
  }

  // === Apply Buttons: AJAX submit ===
  var applyBtns = document.querySelectorAll('.apply-btn');
  applyBtns.forEach(function(btn) {
    btn.addEventListener('click', function() {
      var card = this.closest('.position-card');
      var positionName = card ? card.querySelector('h3').textContent.trim() : '未指定职位';
      var isJapanese = window.location.pathname.indexOf('/ja/') === 0;
      var recipient = isJapanese ? 'idc_info@bl-star.co.jp' : 'info@bl-star.cloud';

      // create modal
      var overlay = document.createElement('div');
      overlay.className = 'apply-modal-overlay';
      var modal = document.createElement('div');
      modal.className = 'apply-modal';
      modal.innerHTML = '<h3>' + (isJapanese ? '応募: ' : '应聘: ') + positionName + '</h3>' +
        '<div class="form-group"><label>' + (isJapanese ? 'お名前 *' : '姓名 *') + '</label><input type="text" id="apply-name" required></div>' +
        '<div class="form-group"><label>' + (isJapanese ? '電話番号 *' : '电话 *') + '</label><input type="tel" id="apply-phone" required></div>' +
        '<div class="form-group"><label>Email *</label><input type="email" id="apply-email" required></div>' +
        '<div class="form-group"><label>' + (isJapanese ? '備考' : '备注') + '</label><textarea id="apply-note" rows="3"></textarea></div>' +
        '<p style="font-size:0.8rem;color:var(--gray-500);margin-bottom:12px;">※ ' + (isJapanese ? '履歴書は別途メールに添付してお送りください。' : '简历请通过附件另行发送至邮箱。') + '</p>' +
        '<div style="display:flex;gap:8px;">' +
        '<button class="btn btn-primary" id="apply-submit" style="flex:1;justify-content:center;">' + (isJapanese ? '送信' : '提交') + '</button>' +
        '<button class="btn" id="apply-cancel" style="flex:1;justify-content:center;background:var(--gray-100);color:var(--gray-700);">' + (isJapanese ? 'キャンセル' : '取消') + '</button></div>';

      overlay.appendChild(modal);
      document.body.appendChild(overlay);
      setTimeout(function() { overlay.classList.add('active'); }, 10);

      document.getElementById('apply-cancel').addEventListener('click', function() {
        overlay.classList.remove('active');
        setTimeout(function() { overlay.remove(); }, 300);
      });
      overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
          overlay.classList.remove('active');
          setTimeout(function() { overlay.remove(); }, 300);
        }
      });

      document.getElementById('apply-submit').addEventListener('click', function() {
        var name = document.getElementById('apply-name').value.trim();
        var phone = document.getElementById('apply-phone').value.trim();
        var email = document.getElementById('apply-email').value.trim();
        var note = document.getElementById('apply-note').value.trim();

        if (!name || !phone || !email) {
          showNotification(isJapanese ? '必須項目を入力してください' : '请填写姓名、电话和邮箱', 'error');
          return;
        }

        this.textContent = isJapanese ? '送信中...' : '发送中...';
        this.disabled = true;

        var data = {
          name: name,
          company: positionName + ' 应聘',
          email: email,
          phone: phone,
          service: '应聘',
          message: '应聘职位: ' + positionName + '\n电话: ' + phone + '\n\n' + note,
          type: 'apply',
          to_email: recipient,
          position: positionName
        };

        var apiUrl = window.location.hostname.indexOf('github.io') === -1 ? 'api/contact.php' : 'https://bl-star.co.jp/api/contact.php';

        fetch(apiUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        })
        .then(function(r) { return r.json(); })
        .then(function(result) {
          overlay.classList.remove('active');
          setTimeout(function() { overlay.remove(); }, 300);
          if (result.status === 'success') {
            showNotification('✓ ' + (isJapanese ? '応募が完了しました。ご連絡をお待ちください。' : '应聘已提交，我们将尽快联系您'), 'success');
          } else {
            showNotification('✗ ' + result.message, 'error');
          }
        })
        .catch(function() {
          showNotification(isJapanese ? '送信エラー' : '发送失败', 'error');
        });
      });
    });
  });

});
