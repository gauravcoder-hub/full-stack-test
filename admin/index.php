<?php 
require '../config/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>DelphianLogic - Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="css/style.css" />
  <meta name="csrf" content="<?= csrfToken(); ?>">
</head>
<body>

  <!-- Sidebar -->
<aside class="sidebar">
  <a href="/full-stack-test" class="sidebar-logo-link">
    <div class="sidebar-logo">
      <i class="fas fa-chart-line"></i>
      <span>DelphianLogic</span>
    </div>
  </a>

  <ul class="sidebar-menu">
    <li>
      <a href="#" class="menu-item active" data-section="header">
        <i class="fas fa-heading"></i> Header
      </a>
    </li>
    <li>
      <a href="#" class="menu-item" data-section="categories">
        <i class="fas fa-folder"></i> Categories
      </a>
    </li>
    <li>
      <a href="#" class="menu-item" data-section="topics">
        <i class="fas fa-file-alt"></i> Topics
      </a>
    </li>
  </ul>
</aside>


  <!-- Main Content -->
  <div class="main-content">
    <!-- HEADER TAB -->
    <div id="section-header" class="section-tab">
      <div class="content-section">
        <div class="section-header">
          <h2><i class="fas fa-heading"></i> Page Header</h2>
          <i class="fas fa-chevron-down toggle-icon"></i>
        </div>
        <div class="section-body">
          <div class="form-group">
            <label>Page Title</label>
            <input type="text" id="siteTitle" placeholder="Enter page title">
          </div>
          <div class="form-group">
            <label>Page Subtitle</label>
            <textarea id="siteDescription" placeholder="Enter page description"></textarea>
          </div>
          <button class="btn-primary-custom" id="saveHeaderBtn">
            <i class="fas fa-save"></i> Save Header
          </button>
        </div>
      </div>
    </div>

    <!-- CATEGORIES TAB -->
    <div id="section-categories" class="section-tab">
      <div class="content-section">
        <div class="section-header">
          <h2><i class="fas fa-folder"></i> Categories</h2>
          <div style="display: flex; gap: 16px; align-items: center;">
            <button class="btn-primary-custom" id="addCategoryBtn" style="margin: 0;">
              <i class="fas fa-plus"></i> Add Category
            </button>
            <i class="fas fa-chevron-down toggle-icon"></i>
          </div>
        </div>
        <div class="section-body">
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Order</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="categoriesList">
                <tr><td colspan="5" class="text-center">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- TOPICS TAB -->
    <div id="section-topics" class="section-tab">
      <div class="content-section">
        <div class="section-header">
          <h2><i class="fas fa-file-alt"></i> Topics</h2>
          <div style="display: flex; gap: 16px; align-items: center;">
            <button class="btn-primary-custom" id="addTopicBtn" style="margin: 0;">
              <i class="fas fa-plus"></i> Add Topic
            </button>
            <i class="fas fa-chevron-down toggle-icon"></i>
          </div>
        </div>
        <div class="section-body">
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Category</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="topicsList">
                <tr><td colspan="5" class="text-center">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>



  </div>

  <!-- Modal for Forms -->
  <div class="modal-backdrop" id="modalBackdrop"></div>
  <div class="modal" id="contentModal">
    <div class="modal-header">
      <h3 id="modalTitle">Add/Edit</h3>
      <button class="modal-close" id="modalClose">×</button>
    </div>
    <div class="modal-body" id="modalBody"></div>
    <div class="modal-footer">
      <button class="btn-cancel" id="modalCancel">Cancel</button>
      <button class="btn-submit" id="modalSubmit">Save</button>
    </div>
  </div>
  <div id="toast-container"></div>

  <script src="js/script.js"></script>
  <script>
    let dataStore = {
      categories: [],
      topics: [],
    };
    class AdminUI {
      constructor() {
        this.setupEventListeners();
        this.loadAllData();
      }

      async loadAllData() {
        await Promise.all([
          this.loadHeader(),
          this.loadCategories(),
          this.loadTopics(),
        ]);
      }

      async loadHeader() {
        const res = await api('header.get');
        if (res.success) {
          document.getElementById('siteTitle').value = res.data.site_title || '';
          document.getElementById('siteDescription').value = res.data.site_description || '';
        }
      }

      async loadCategories() {
        const res = await api('category.list');
        if (res.success) {
          dataStore.categories = res.data;
          this.renderCategories();
        }
      }

      async loadTopics() {
        const res = await api('topic.list');
        if (res.success) {
          dataStore.topics = res.data;
          this.renderTopics();
        }
      }


 


      setupEventListeners() {
        // Menu items
        document.querySelectorAll('.menu-item').forEach(item => {
          item.addEventListener('click', (e) => {
            e.preventDefault();
            this.switchSection(item.dataset.section);
          });
        });

        // Section header collapse
        document.querySelectorAll('.section-header').forEach(header => {
          header.addEventListener('click', (e) => {
            if (e.target.closest('.btn-primary-custom')) return;
            const body = header.nextElementSibling;
            header.classList.toggle('collapsed');
            body.classList.toggle('collapsed');
          });
        });

        // Button listeners
        document.getElementById('saveHeaderBtn').addEventListener('click', () => this.saveHeader());
        document.getElementById('addCategoryBtn').addEventListener('click', () => this.showCategoryModal());
        document.getElementById('addTopicBtn').addEventListener('click', () => this.showTopicModal());

        // Modal
        document.getElementById('modalClose').addEventListener('click', () => this.closeModal());
        document.getElementById('modalCancel').addEventListener('click', () => this.closeModal());
        document.getElementById('modalBackdrop').addEventListener('click', () => this.closeModal());
      }

      async saveHeader() {
        const payload = {
          site_title: document.getElementById('siteTitle').value,
          site_description: document.getElementById('siteDescription').value
        };

        const res = await api('header.save', payload);
        if (res.success) {
          Swal.fire('Success', res.message, 'success');
        } else {
          Swal.fire('Error', res.message, 'error');
        }
      }

      switchSection(section) {
        document.querySelectorAll('.section-tab').forEach(tab => tab.style.display = 'none');
        document.getElementById(`section-${section}`).style.display = 'block';
        document.querySelectorAll('.menu-item').forEach(item => item.classList.remove('active'));
        document.querySelector(`[data-section="${section}"]`).classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }

      renderCategories() {
        const tbody = document.getElementById('categoriesList');
        if (!dataStore.categories.length) {
          tbody.innerHTML = '<tr><td colspan="5" class="text-center">No categories found</td></tr>';
          return;
        }
        tbody.innerHTML = dataStore.categories.map(cat => `
          <tr>
            <td><strong>${cat.name}</strong></td>
            <td>${cat.description || ''}</td>
            <td><span class="status-badge ${cat.is_active ? 'status-active' : 'status-inactive'}">${cat.is_active ? '✓ Active' : '✗ Inactive'}</span></td>
            <td>${cat.display_order}</td>
            <td>
              <div class="action-buttons">
                <button class="btn-edit" onclick="adminUI.editCategory(${cat.id})"><i class="fas fa-edit"></i> Edit</button>
                <button class="btn-delete" onclick="adminUI.deleteCategory(${cat.id})"><i class="fas fa-trash"></i> Delete</button>
              </div>
            </td>
          </tr>
        `).join('');
      }

      renderTopics() {
        const tbody = document.getElementById('topicsList');
        if (!dataStore.topics.length) {
          tbody.innerHTML = '<tr><td colspan="5" class="text-center">No topics found</td></tr>';
          return;
        }
        tbody.innerHTML = dataStore.topics.map(topic => {
          const category = dataStore.categories.find(c => c.id === topic.category_id);
          return `
            <tr>
              <td><strong>${topic.title.substring(0, 35)}...</strong></td>
              <td>${category?.name || 'N/A'}</td>
              <td><span class="status-badge ${topic.is_active ? 'status-active' : 'status-inactive'}">${topic.is_active ? '✓ Active' : '✗ Inactive'}</span></td>
              <td>
                <div class="action-buttons">
                  <button class="btn-edit" onclick="adminUI.editTopic(${topic.id})"><i class="fas fa-edit"></i> Edit</button>
                  <button class="btn-delete" onclick="adminUI.deleteTopic(${topic.id})"><i class="fas fa-trash"></i> Delete</button>
                </div>
              </td>
            </tr>
          `;
        }).join('');
      }



      showModal(title, formHTML) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalBody').innerHTML = formHTML;
        document.getElementById('contentModal').classList.add('active');
        document.getElementById('modalBackdrop').classList.add('active');
      }

      closeModal() {
        document.getElementById('contentModal').classList.remove('active');
        document.getElementById('modalBackdrop').classList.remove('active');
      }

    showCategoryModal(id = null) {
  const category = id ? dataStore.categories.find(c => c.id === id) : null;

  const iconPreview = category?.icon_url
    ? `<img src="/full-stack-test/${category.icon_url}" id="iconPreviewImg"
        class="rounded border"
        style="width:80px;height:80px;object-fit:cover;cursor:pointer;">`
    : `<div id="iconPlaceholder"
        class="border rounded d-flex align-items-center justify-content-center text-muted"
        style="width:80px;height:80px;cursor:pointer;">
        +
      </div>`;

  const html = `
    <div class="row g-3">

      <div class="col-md-6">
        <label class="form-label">Category Name</label>
        <input type="text" class="form-control" id="catName"
          value="${category?.name || ''}" placeholder="e.g. Learning">
      </div>

      <div class="col-md-6">
        <label class="form-label">Display Order</label>
        <input type="number" class="form-control" id="catOrder"
          value="${category?.display_order || 1}">
      </div>

      <div class="col-md-6 d-flex align-items-center">
        <div class="form-check mt-4">
          <input class="form-check-input" type="checkbox" id="catActive"
            ${category?.is_active ? 'checked' : ''}>
          <label class="form-check-label">Active</label>
        </div>
      </div>

      <div class="col-md-12">
        <label class="form-label">Description</label>
        <textarea class="form-control" id="catDesc" rows="3"
          placeholder="Brief description">${category?.description || ''}</textarea>
      </div>

      <!-- Icon Upload -->
      <div class="col-md-6">
        <label class="form-label d-block">Category Icon</label>

        <input type="file" id="catIconFile"
          accept="image/*" hidden>

        <div id="iconPreviewWrapper"
          class="d-inline-block"
          onclick="document.getElementById('catIconFile').click()">
          ${iconPreview}
        </div>

        <small class="text-muted d-block mt-1">
          Click image to upload / change
        </small>
      </div>

    </div>
  `;

  this.showModal(id ? 'Edit Category' : 'Add Category', html);

  // Preview logic (WhatsApp-style)
  document.getElementById('catIconFile').addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = ev => {
      document.getElementById('iconPreviewWrapper').innerHTML = `
        <img src="${ev.target.result}"
          class="rounded border"
          style="width:80px;height:80px;object-fit:cover;cursor:pointer;">
      `;
    };
    reader.readAsDataURL(file);
  });

  document.getElementById('modalSubmit').onclick = () => this.saveCategory(id);
}


   async saveCategory(id) {
  const name = document.getElementById('catName').value.trim();

  if (!name) {
    Swal.fire('Error', 'Category name is required', 'error');
    return;
  }

  const payload = {
    name,
    description: document.getElementById('catDesc').value.trim(),
    display_order: parseInt(document.getElementById('catOrder').value) || 1,
    is_active: document.getElementById('catActive').checked ? 1 : 0
  };

  const fileInput = document.getElementById('catIconFile');
  if (fileInput && fileInput.files.length > 0) {
    payload.icon = fileInput.files[0]; // IMPORTANT: File object
  }

  if (id) {
    payload.id = id;
  }

  const res = await api('category.save', payload);

  if (res.success) {
    this.closeModal();
    await this.loadCategories();
    Swal.fire('Success', res.message, 'success');
  } else {
    Swal.fire('Error', res.message || 'Something went wrong', 'error');
  }
}

      editCategory(id) {
        this.showCategoryModal(id);
      }

      async deleteCategory(id) {
        const result = await Swal.fire({
          title: 'Delete Category?',
          text: 'This action cannot be undone.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
          const res = await api('category.delete', { id });
          if (res.success) {
            await this.loadCategories();
            Swal.fire('Deleted!', res.message, 'success');
          } else {
            Swal.fire('Error', res.message, 'error');
          }
        }
      }

      showTopicModal(id = null) {
        const topic = id ? dataStore.topics.find(t => t.id === id) : null;
        const categoryOptions = dataStore.categories.map(cat => 
          `<option value="${cat.id}" ${topic?.category_id === cat.id ? 'selected' : ''}>${cat.name}</option>`
        ).join('');

        const html = `
          <div class="form-group">
            <label>Category</label>
            <select id="topicCategory" class="form-control">${categoryOptions}</select>
          </div>
          <div class="form-group">
            <label>Topic Title</label>
            <input type="text" class="form-control" id="topicTitle" value="${topic?.title || ''}" placeholder="Enter topic title">
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" id="topicDesc" rows="3">${topic?.description || ''}</textarea>
          </div>
          <div class="form-group">
            <label><input type="checkbox" id="topicActive" ${topic?.is_active ? 'checked' : ''} style="margin-right: 8px;"> Active</label>
          </div>
        `;
        this.showModal(id ? 'Edit Topic' : 'Add Topic', html);
        document.getElementById('modalSubmit').onclick = () => this.saveTopic(id);
      }

      async saveTopic(id) {
        const topicData = {
          category_id: parseInt(document.getElementById('topicCategory').value),
          title: document.getElementById('topicTitle').value,
          description: document.getElementById('topicDesc').value,
          is_active: document.getElementById('topicActive').checked ? 1 : 0
        };
        if (!topicData.category_id) {
  Swal.fire('Error', 'Please select a category', 'error');
  return;
}

        if (!topicData.title) {
          Swal.fire('Error', 'Topic title is required', 'error');
          return;
        }

        if (id) topicData.id = id;

        const res = await api('topic.save', topicData);
        if (res.success) {
          this.closeModal();
          await this.loadTopics();
          Swal.fire('Success', res.message, 'success');
        } else {
          Swal.fire('Error', res.message, 'error');
        }
      }

      editTopic(id) {
        this.showTopicModal(id);
      }

      async deleteTopic(id) {
        const result = await Swal.fire({
          title: 'Delete Topic?',
          text: 'All related content will also be deleted. This cannot be undone.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
          const res = await api('topic.delete', { id });
          if (res.success) {
            await Promise.all([
              this.loadTopics()
            ]);
            Swal.fire('Deleted!', res.message, 'success');
          } else {
            Swal.fire('Error', res.message, 'error');
          }
        }
      }

    }

    // Initialize Admin UI
    const adminUI = new AdminUI();
  </script>
</body>
</html>