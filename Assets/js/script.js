

    const deviceToggle = document.getElementById('deviceToggle');
    const mobileView = document.getElementById('mobileView');
    const desktopView = document.getElementById('desktopView');
    let isDesktopMode = window.innerWidth >= 992;

    // Initialize based on screen size
    function initializeView() {
      if (window.innerWidth >= 992) {
        isDesktopMode = true;
        deviceToggle.classList.add('active');
        mobileView.classList.add('hidden');
        desktopView.classList.add('active');
      } else {
        isDesktopMode = false;
        deviceToggle.classList.remove('active');
        mobileView.classList.remove('hidden');
        desktopView.classList.remove('active');
      }
    }

    // Toggle device view
    deviceToggle.addEventListener('click', () => {
      isDesktopMode = !isDesktopMode;
      deviceToggle.classList.toggle('active');
      
      if (isDesktopMode) {
        mobileView.classList.add('hidden');
        desktopView.classList.add('active');
      } else {
        mobileView.classList.remove('hidden');
        desktopView.classList.remove('active');
      }
    });

    // Mobile accordion functionality
    const accordionItems = document.querySelectorAll('.accordion-item-custom');
    
    function updateIconForItem(item) {
      const toggleIcon = item.querySelector('.toggle-icon img');
      const isExpanded = item.classList.contains('expanded');
      
      if (isExpanded) {
        toggleIcon.src = 'Assets/images/minus-01.svg';
        toggleIcon.alt = 'Minus Icon';
      } else {
        toggleIcon.src = 'Assets/images/plus-01.svg';
        toggleIcon.alt = 'Plus Icon';
      }
    }
    
    accordionItems.forEach(item => {
      const header = item.querySelector('.accordion-header-custom');
      header.addEventListener('click', () => {
        accordionItems.forEach(otherItem => {
          if (otherItem !== item && otherItem.classList.contains('expanded')) {
            otherItem.classList.remove('expanded');
            updateIconForItem(otherItem);
          }
        });
        item.classList.toggle('expanded');
        updateIconForItem(item);
      });
    });

    // Desktop tab and slider functionality
    const tabs = document.querySelectorAll('.feature-tab');
    const dots = document.querySelectorAll('.slider-dots span');
    const sliderContent = document.querySelector('.slider-content');
    
    const contentData = [
      {
        tag: 'DIGITAL LEARNING INFRASTRUCTURE',
        title: 'Usability enhancement and Training for Transaction Portal for Customers',
        desc: 'Our comprehensive training program ensures seamless adoption of the transaction portal with enhanced usability features.'
      },
      {
        tag: 'TECHNOLOGY INFRASTRUCTURE',
        title: 'Advanced Technology Solutions for Modern Business',
        desc: 'Leverage cutting-edge technology infrastructure to streamline operations, enhance security, and drive innovation.'
      },
      {
        tag: 'COMMUNICATION FRAMEWORK',
        title: 'Seamless Communication Channels for Better Engagement',
        desc: 'Connect with customers through multiple channels with our integrated communication framework for engagement.'
      }
    ];

    function updateSlider(index) {
      tabs.forEach((tab, i) => {
        tab.classList.toggle('active', i === index);
      });
      
      dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
      });

      const data = contentData[index];
      sliderContent.innerHTML = `
        <small>${data.tag}</small>
        <h2>${data.title}</h2>
        <p>${data.desc}</p>
        <a href="#" class="learn-more">Learn More →</a>
      `;
    }

    tabs.forEach((tab, index) => {
      tab.addEventListener('click', () => updateSlider(index));
    });

    dots.forEach((dot, index) => {
      dot.addEventListener('click', () => updateSlider(index));
    });

    // Insurance toggle functionality
    const insuranceToggle = document.getElementById('insuranceToggle');
    if (insuranceToggle) {
      insuranceToggle.addEventListener('click', () => {
        insuranceToggle.classList.toggle('active');
      });
    }

    // Handle window resize
    window.addEventListener('resize', initializeView);

    // Initialize
    initializeView();
