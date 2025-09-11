import { ref, computed } from 'vue';
import { productsApi } from '../services/api.js';

/**
 * Composable pour la gestion des uploads d'images
 * @returns {Object} État et méthodes pour gérer les uploads
 */
export function useImageUpload() {
  const uploading = ref(false);
  const uploadProgress = ref(0);
  const uploadedImages = ref([]);
  const error = ref(null);

  // Computed
  const isUploading = computed(() => uploading.value);
  const hasError = computed(() => error.value !== null);
  const hasImages = computed(() => uploadedImages.value.length > 0);

  /**
   * Valider un fichier image
   * @param {File} file - Fichier à valider
   * @returns {Object} Résultat de validation
   */
  const validateFile = (file) => {
    const maxSize = 10 * 1024 * 1024; // 10MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!file) {
      return { valid: false, error: 'Aucun fichier sélectionné' };
    }
    
    if (!allowedTypes.includes(file.type)) {
      return { 
        valid: false, 
        error: 'Type de fichier non autorisé. Formats acceptés: JPEG, PNG, GIF, WebP' 
      };
    }
    
    if (file.size > maxSize) {
      return { 
        valid: false, 
        error: 'Fichier trop volumineux. Taille maximum: 10MB' 
      };
    }
    
    return { valid: true };
  };

  /**
   * Upload d'une seule image
   * @param {File} file - Fichier image
   * @param {number} productId - ID du produit (optionnel)
   * @param {string} altText - Texte alternatif
   * @returns {Promise} URL de l'image uploadée
   */
  const uploadSingleImage = async (file, productId = null, altText = '') => {
    // Validation
    const validation = validateFile(file);
    if (!validation.valid) {
      error.value = validation.error;
      throw new Error(validation.error);
    }

    try {
      uploading.value = true;
      error.value = null;
      uploadProgress.value = 0;

      const response = await productsApi.uploadImage(file, productId, altText);
      
      if (response.success) {
        const imageData = {
          url: response.data.url,
          alt_text: altText || file.name,
          file_name: file.name,
          file_size: file.size,
          uploaded_at: new Date().toISOString()
        };
        
        uploadedImages.value.push(imageData);
        uploadProgress.value = 100;
        
        return imageData;
      } else {
        throw new Error(response.error || 'Erreur lors de l\'upload');
      }
    } catch (err) {
      error.value = err.message;
      console.error('Erreur uploadSingleImage:', err);
      throw err;
    } finally {
      uploading.value = false;
    }
  };

  /**
   * Upload de plusieurs images
   * @param {FileList|Array} files - Liste des fichiers
   * @param {number} productId - ID du produit (optionnel)
   * @returns {Promise} Liste des images uploadées
   */
  const uploadMultipleImages = async (files, productId = null) => {
    const fileArray = Array.from(files);
    const results = [];
    const errors = [];

    try {
      uploading.value = true;
      error.value = null;

      for (let i = 0; i < fileArray.length; i++) {
        const file = fileArray[i];
        uploadProgress.value = Math.round((i / fileArray.length) * 100);

        try {
          const result = await uploadSingleImage(file, productId, file.name);
          results.push(result);
        } catch (err) {
          errors.push({
            file: file.name,
            error: err.message
          });
        }
      }

      uploadProgress.value = 100;

      if (errors.length > 0) {
        const errorMessage = `Erreurs lors de l'upload: ${errors.map(e => `${e.file}: ${e.error}`).join(', ')}`;
        error.value = errorMessage;
      }

      return {
        success: results,
        errors: errors,
        total: fileArray.length
      };
    } catch (err) {
      error.value = err.message;
      console.error('Erreur uploadMultipleImages:', err);
      throw err;
    } finally {
      uploading.value = false;
    }
  };

  /**
   * Supprimer une image uploadée
   * @param {number} index - Index de l'image dans la liste
   */
  const removeUploadedImage = (index) => {
    if (index >= 0 && index < uploadedImages.value.length) {
      uploadedImages.value.splice(index, 1);
    }
  };

  /**
   * Créer une prévisualisation d'image
   * @param {File} file - Fichier image
   * @returns {Promise} URL de prévisualisation
   */
  const createPreview = (file) => {
    return new Promise((resolve, reject) => {
      if (!file || !file.type.startsWith('image/')) {
        reject(new Error('Fichier invalide'));
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => resolve(e.target.result);
      reader.onerror = () => reject(new Error('Erreur lors de la lecture du fichier'));
      reader.readAsDataURL(file);
    });
  };

  /**
   * Redimensionner une image avant upload
   * @param {File} file - Fichier image
   * @param {number} maxWidth - Largeur maximum
   * @param {number} maxHeight - Hauteur maximum
   * @param {number} quality - Qualité (0-1)
   * @returns {Promise} Fichier redimensionné
   */
  const resizeImage = (file, maxWidth = 1200, maxHeight = 1200, quality = 0.8) => {
    return new Promise((resolve) => {
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');
      const img = new Image();

      img.onload = () => {
        // Calculer les nouvelles dimensions
        let { width, height } = img;
        
        if (width > height) {
          if (width > maxWidth) {
            height = (height * maxWidth) / width;
            width = maxWidth;
          }
        } else {
          if (height > maxHeight) {
            width = (width * maxHeight) / height;
            height = maxHeight;
          }
        }

        canvas.width = width;
        canvas.height = height;

        // Dessiner l'image redimensionnée
        ctx.drawImage(img, 0, 0, width, height);

        // Convertir en blob
        canvas.toBlob(
          (blob) => {
            const resizedFile = new File([blob], file.name, {
              type: file.type,
              lastModified: Date.now()
            });
            resolve(resizedFile);
          },
          file.type,
          quality
        );
      };

      img.src = URL.createObjectURL(file);
    });
  };

  /**
   * Réinitialiser l'état
   */
  const reset = () => {
    uploading.value = false;
    uploadProgress.value = 0;
    uploadedImages.value = [];
    error.value = null;
  };

  /**
   * Effacer les erreurs
   */
  const clearError = () => {
    error.value = null;
  };

  return {
    // État
    uploading,
    uploadProgress,
    uploadedImages,
    error,
    
    // Computed
    isUploading,
    hasError,
    hasImages,
    
    // Méthodes
    validateFile,
    uploadSingleImage,
    uploadMultipleImages,
    removeUploadedImage,
    createPreview,
    resizeImage,
    reset,
    clearError
  };
}