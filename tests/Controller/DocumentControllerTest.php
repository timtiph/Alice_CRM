<?php

namespace App\Test\Controller;

use App\Entity\Document;
use App\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DocumentControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private DocumentRepository $repository;
    private string $path = '/document/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Document::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Document index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'document[name]' => 'Testing',
            'document[description]' => 'Testing',
            'document[date]' => 'Testing',
            'document[fileName]' => 'Testing',
            'document[family]' => 'Testing',
            'document[user]' => 'Testing',
        ]);

        self::assertResponseRedirects('/document/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Document();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setDate('My Title');
        $fixture->setFileName('My Title');
        $fixture->setFamily('My Title');
        $fixture->setUser('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Document');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Document();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setDate('My Title');
        $fixture->setFileName('My Title');
        $fixture->setFamily('My Title');
        $fixture->setUser('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'document[name]' => 'Something New',
            'document[description]' => 'Something New',
            'document[date]' => 'Something New',
            'document[fileName]' => 'Something New',
            'document[family]' => 'Something New',
            'document[user]' => 'Something New',
        ]);

        self::assertResponseRedirects('/document/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getDate());
        self::assertSame('Something New', $fixture[0]->getFileName());
        self::assertSame('Something New', $fixture[0]->getFamily());
        self::assertSame('Something New', $fixture[0]->getUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Document();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setDate('My Title');
        $fixture->setFileName('My Title');
        $fixture->setFamily('My Title');
        $fixture->setUser('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/document/');
    }
}
