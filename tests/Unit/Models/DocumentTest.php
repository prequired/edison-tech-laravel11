<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Contract;
use App\Models\Document;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Document Model Unit Tests
 *
 * Tests the Document model including:
 * - Factory creation
 * - Polymorphic relationships (documentable)
 * - Uploader relationship
 * - File metadata handling
 * - Public/private documents
 * - Mass assignment
 * - Type casting
 * - Soft deletes
 */
class DocumentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test document can be created using factory
     */
    public function test_document_can_be_created_using_factory(): void
    {
        $document = Document::factory()->create();

        $this->assertInstanceOf(Document::class, $document);
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
        ]);
    }

    /**
     * Test document belongs to uploader
     */
    public function test_document_belongs_to_uploader(): void
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['uploaded_by' => $user->id]);

        $this->assertInstanceOf(User::class, $document->uploader);
        $this->assertEquals($user->id, $document->uploader->id);
    }

    /**
     * Test document can belong to project
     */
    public function test_document_can_belong_to_project(): void
    {
        $project = Project::factory()->create();
        $document = Document::factory()->create([
            'documentable_type' => Project::class,
            'documentable_id' => $project->id,
        ]);

        $this->assertInstanceOf(Project::class, $document->documentable);
        $this->assertEquals($project->id, $document->documentable->id);
    }

    /**
     * Test document can belong to contract
     */
    public function test_document_can_belong_to_contract(): void
    {
        $contract = Contract::factory()->create();
        $document = Document::factory()->create([
            'documentable_type' => Contract::class,
            'documentable_id' => $contract->id,
        ]);

        $this->assertInstanceOf(Contract::class, $document->documentable);
        $this->assertEquals($contract->id, $document->documentable->id);
    }

    /**
     * Test document can be orphaned (no parent)
     */
    public function test_document_can_be_orphaned(): void
    {
        $document = Document::factory()->create([
            'documentable_type' => null,
            'documentable_id' => null,
        ]);

        $this->assertNull($document->documentable_type);
        $this->assertNull($document->documentable_id);
        $this->assertNull($document->documentable);
    }

    /**
     * Test document has name
     */
    public function test_document_has_name(): void
    {
        $document = Document::factory()->create([
            'name' => 'Project Requirements',
        ]);

        $this->assertEquals('Project Requirements', $document->name);
    }

    /**
     * Test document has filename
     */
    public function test_document_has_filename(): void
    {
        $document = Document::factory()->create([
            'filename' => 'requirements.pdf',
        ]);

        $this->assertEquals('requirements.pdf', $document->filename);
    }

    /**
     * Test document has file path
     */
    public function test_document_has_file_path(): void
    {
        $document = Document::factory()->create([
            'file_path' => 'documents/2025/01/requirements.pdf',
        ]);

        $this->assertEquals('documents/2025/01/requirements.pdf', $document->file_path);
    }

    /**
     * Test document has mime type
     */
    public function test_document_has_mime_type(): void
    {
        $document = Document::factory()->create([
            'mime_type' => 'application/pdf',
        ]);

        $this->assertEquals('application/pdf', $document->mime_type);
    }

    /**
     * Test different mime types
     */
    public function test_different_mime_types(): void
    {
        $mimeTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'application/vnd.ms-excel',
            'application/msword',
            'text/plain',
        ];

        foreach ($mimeTypes as $mimeType) {
            $document = Document::factory()->create(['mime_type' => $mimeType]);
            $this->assertEquals($mimeType, $document->mime_type);
        }
    }

    /**
     * Test document file size is cast to integer
     */
    public function test_document_file_size_is_cast_to_integer(): void
    {
        $document = Document::factory()->create(['file_size' => 1024000]);

        $this->assertIsInt($document->file_size);
        $this->assertEquals(1024000, $document->file_size);
    }

    /**
     * Test document file size in bytes
     */
    public function test_document_file_size_in_bytes(): void
    {
        $document = Document::factory()->create(['file_size' => 5242880]); // 5MB

        $this->assertEquals(5242880, $document->file_size);
        $this->assertEquals(5, round($document->file_size / 1024 / 1024));
    }

    /**
     * Test document can have category
     */
    public function test_document_can_have_category(): void
    {
        $document = Document::factory()->create([
            'category' => 'contract',
        ]);

        $this->assertEquals('contract', $document->category);
    }

    /**
     * Test document category can be null
     */
    public function test_document_category_can_be_null(): void
    {
        $document = Document::factory()->create(['category' => null]);

        $this->assertNull($document->category);
    }

    /**
     * Test different document categories
     */
    public function test_different_document_categories(): void
    {
        $categories = ['contract', 'invoice', 'proposal', 'report', 'specification'];

        foreach ($categories as $category) {
            $document = Document::factory()->create(['category' => $category]);
            $this->assertEquals($category, $document->category);
        }
    }

    /**
     * Test document can have description
     */
    public function test_document_can_have_description(): void
    {
        $document = Document::factory()->create([
            'description' => 'Detailed project specifications and requirements',
        ]);

        $this->assertEquals('Detailed project specifications and requirements', $document->description);
    }

    /**
     * Test document description can be null
     */
    public function test_document_description_can_be_null(): void
    {
        $document = Document::factory()->create(['description' => null]);

        $this->assertNull($document->description);
    }

    /**
     * Test document is public flag is cast to boolean
     */
    public function test_document_is_public_flag_is_cast_to_boolean(): void
    {
        $document = Document::factory()->create(['is_public' => true]);

        $this->assertIsBool($document->is_public);
        $this->assertTrue($document->is_public);
    }

    /**
     * Test document can be private
     */
    public function test_document_can_be_private(): void
    {
        $document = Document::factory()->create(['is_public' => false]);

        $this->assertFalse($document->is_public);
    }

    /**
     * Test document can be public
     */
    public function test_document_can_be_public(): void
    {
        $document = Document::factory()->create(['is_public' => true]);

        $this->assertTrue($document->is_public);
    }

    /**
     * Test document uses soft deletes
     */
    public function test_document_uses_soft_deletes(): void
    {
        $document = Document::factory()->create();

        $document->delete();

        $this->assertSoftDeleted('documents', [
            'id' => $document->id,
        ]);

        $this->assertNotNull($document->fresh()->deleted_at);
    }

    /**
     * Test soft deleted documents can be restored
     */
    public function test_soft_deleted_documents_can_be_restored(): void
    {
        $document = Document::factory()->create();
        $document->delete();

        $document->restore();

        $this->assertNull($document->fresh()->deleted_at);
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test document has timestamps
     */
    public function test_document_has_timestamps(): void
    {
        $document = Document::factory()->create();

        $this->assertNotNull($document->created_at);
        $this->assertNotNull($document->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $document->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $document->updated_at);
    }

    /**
     * Test document mass assignment
     */
    public function test_document_mass_assignment(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $document = Document::create([
            'documentable_type' => Project::class,
            'documentable_id' => $project->id,
            'uploaded_by' => $user->id,
            'name' => 'Test Document',
            'filename' => 'test.pdf',
            'file_path' => 'documents/test.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 102400,
            'category' => 'specification',
            'description' => 'Test description',
            'is_public' => false,
        ]);

        $this->assertEquals('Test Document', $document->name);
        $this->assertEquals('test.pdf', $document->filename);
        $this->assertEquals('application/pdf', $document->mime_type);
        $this->assertFalse($document->is_public);
    }

    /**
     * Test document can be updated
     */
    public function test_document_can_be_updated(): void
    {
        $document = Document::factory()->create([
            'name' => 'Original Name',
            'is_public' => false,
        ]);

        $document->update([
            'name' => 'Updated Name',
            'is_public' => true,
            'category' => 'report',
        ]);

        $this->assertEquals('Updated Name', $document->name);
        $this->assertTrue($document->is_public);
        $this->assertEquals('report', $document->category);
    }

    /**
     * Test multiple documents for same entity
     */
    public function test_multiple_documents_for_same_entity(): void
    {
        $project = Project::factory()->create();

        Document::factory()->count(3)->create([
            'documentable_type' => Project::class,
            'documentable_id' => $project->id,
        ]);

        $this->assertCount(3, $project->documents);
    }

    /**
     * Test document uploaded by different users
     */
    public function test_document_uploaded_by_different_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $doc1 = Document::factory()->create(['uploaded_by' => $user1->id]);
        $doc2 = Document::factory()->create(['uploaded_by' => $user2->id]);

        $this->assertEquals($user1->id, $doc1->uploader->id);
        $this->assertEquals($user2->id, $doc2->uploader->id);
    }

    /**
     * Test document name and filename can differ
     */
    public function test_document_name_and_filename_can_differ(): void
    {
        $document = Document::factory()->create([
            'name' => 'Project Requirements Document',
            'filename' => 'req_v1_final_FINAL.pdf',
        ]);

        $this->assertNotEquals($document->name, $document->filename);
        $this->assertEquals('Project Requirements Document', $document->name);
        $this->assertEquals('req_v1_final_FINAL.pdf', $document->filename);
    }

    /**
     * Test large file size handling
     */
    public function test_large_file_size_handling(): void
    {
        $document = Document::factory()->create([
            'file_size' => 104857600, // 100MB
        ]);

        $this->assertEquals(104857600, $document->file_size);
        $this->assertEquals(100, $document->file_size / 1024 / 1024);
    }

    /**
     * Test polymorphic relationship type
     */
    public function test_polymorphic_relationship_type(): void
    {
        $project = Project::factory()->create();
        $contract = Contract::factory()->create();

        $projectDoc = Document::factory()->create([
            'documentable_type' => Project::class,
            'documentable_id' => $project->id,
        ]);

        $contractDoc = Document::factory()->create([
            'documentable_type' => Contract::class,
            'documentable_id' => $contract->id,
        ]);

        $this->assertEquals(Project::class, $projectDoc->documentable_type);
        $this->assertEquals(Contract::class, $contractDoc->documentable_type);
    }

    /**
     * Test file path contains slashes
     */
    public function test_file_path_contains_slashes(): void
    {
        $document = Document::factory()->create([
            'file_path' => 'documents/2025/01/15/project-requirements.pdf',
        ]);

        $this->assertStringContainsString('/', $document->file_path);
        $this->assertStringContainsString('2025', $document->file_path);
    }

    /**
     * Test document category filter
     */
    public function test_document_category_filter(): void
    {
        Document::factory()->create(['category' => 'contract']);
        Document::factory()->create(['category' => 'invoice']);
        Document::factory()->create(['category' => 'contract']);

        $contracts = Document::where('category', 'contract')->get();

        $this->assertCount(2, $contracts);
    }

    /**
     * Test public documents filter
     */
    public function test_public_documents_filter(): void
    {
        Document::factory()->create(['is_public' => true]);
        Document::factory()->create(['is_public' => false]);
        Document::factory()->create(['is_public' => true]);

        $publicDocs = Document::where('is_public', true)->get();

        $this->assertCount(2, $publicDocs);
    }
}
